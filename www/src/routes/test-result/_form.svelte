<script>
    import { onMount } from 'svelte';
    import { goto, stores } from '@sapper/app'
    import * as alert from '../../components/alert/alert.js';
    import ActiveTextInput from '../../components/input/ActiveText.svelte';
    import ActiveDropdown from '../../components/input/ActiveDropdown.svelte';
    import DetailedRow from '../../components/DetailedRow.svelte';
    import Button from '../../components/Button.svelte';
    import AutoComplete from '../../components/input/AutoComplete.svelte';
    import ActiveAutoComplete from '../../components/input/ActiveAutoComplete.svelte';
    import RichEditor from '../../components/input/RichEditor.svelte';
    import validate from 'validate.js';
    import * as UserApi from '@app/api/user';
    import axios from 'axios';
    import {
        STATUS_ACTIVE,
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_UNABLE_TO_TEST,
    } from '@app/constants';

    export let model;
    export let testCase;

    const { session } = stores();
    const { token } = $session;
    const APP_URL = QA_APP_URL;

    let isLoading = false;
    async function handleSubmit() {
        if (model.validateForm() === false) {
            model = model;
            return false;
        }
        isLoading = true;
        let isNewRecord = model.isNewRecord();
        let action = isNewRecord ? 'created' : 'updated';
        if (await model.save() === true) {
            const link = `/issue/${testCase.issue_id}?tab=${testCase.id}&result=${model.getId()}`;
            switch (model.get('test_status')) {
                case TEST_STATUS_PASSED:
                    alert.success(`🎉 ${testCase.short_description}: ${TEST_STATUS_PASSED}`);
                    break;
                case TEST_STATUS_FAILED:
                    try {
                        axios.post('lark/message', {
                            issueNumber: testCase.issue.jira_number, 
                            testIssue: testCase.issue.name,
                            testCase: testCase.short_description,
                            url: `${APP_URL}${link}`,
                            devLarkId: testCase.issue.developer ? testCase.issue.developer.lark_id : null, 
                            qaLarkId: testCase.issue.qa ? testCase.issue.qa.lark_id : null
                        });
                    } catch (error) {
                        
                    }
                    alert.danger(`🚨 ${testCase.short_description}: ${TEST_STATUS_FAILED}`);
                    break;
                case TEST_STATUS_UNABLE_TO_TEST:
                    alert.warning(`🤔 ${testCase.short_description}: ${TEST_STATUS_UNABLE_TO_TEST}`);
                    break;
            }
            try {
                if (testCase.issue.jira_number) {
                    axios.post('jira/comment', {
                        issueId: testCase.issue_id,
                        issue: testCase.issue.jira_number, 
                        description: testCase.short_description,
                        status: model.get('test_status'), 
                        resultLink: `${APP_URL}${link}`,
                        issueLink: `${APP_URL}/issue/${testCase.issue_id}`
                    });
                }
            } catch (error) {
                console.error('Unable to post JIRA comment');
            }
            goto(link);
        } else if (!model.hasErrors()) {
            alert.danger(`❗️Unable to ${action}. Some server error.`);
        }
        isLoading = false; 
        model = model;
        return false;
    }

    function resetForm() {
        model.reset();
        model = model;
    }

</script>

<form on:submit|preventDefault={handleSubmit}>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('version')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="1.2.3 (456)"
                    attribute="version"
                    label={false} 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('platform')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="platform"
                    label={false}
                    placeholder="iOS / Android / Doctor / CMS / ..."
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('test_status')}
            </div>
            <div slot="content">
                <ActiveDropdown 
                    {model}
                    attribute="test_status" 
                    label={false}
                    options={model.testStatusOptions}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('actual_result')}
            </div>
            <div slot="content">
                <RichEditor 
                    {token}
                    {model}
                    attribute="actual_result"
                    label={false}
                    placeholder="Actual test result..."
                />
            </div>
        </DetailedRow>
    </div>

    <div class="">
        <Button 
            label="{model.isNewRecord() ? 'Save' : 'Update'}" 
            type="submit" 
            color="{model.isNewRecord() ? 'green' : 'blue'}"
            isLoading={isLoading} 
            disabled={model.hasErrors()}
        />

        <Button 
            on:click={resetForm}
            buttonClass="ml-2"
            label="Reset" 
            type="button"
            color="gray"
        />
    </div>
</form>

