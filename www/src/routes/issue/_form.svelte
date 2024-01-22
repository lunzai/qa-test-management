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
    import Checkbox from '../../components/input/Checkbox.svelte';
    import validate from 'validate.js';
    import * as UserApi from '@app/api/user';
    import * as GroupApi from '@app/api/testGroup';
    import { JIRA_WEB_URL } from '@app/configs';

    export let model;
    export let group;
    export let token;

    const { page } = stores();
    const { query } = $page;
    const isNewRecord = model.isNewRecord();
    let isLoading = false;
    let userOptions = [];
    let groupOptions = [];
    let createAnother = query.createAnother === undefined ? false : true;
    
    onMount(async () => {
        // get user options for dropdown
        const users = await UserApi.getActiveUserList(token);
        if (users.success) {
            userOptions = users.data.items.reduce((acc, { id, display_name, job_role }) => {
                acc.push({
                    label: `${display_name} (${job_role})`,
                    value: id,
                });
                return acc;
            }, []);
        }
        const groups = await GroupApi.getGroupList(token);
        if (groups.success) {
            groupOptions = groups.data.items.reduce((acc, { id, name, test_status }) => {
                acc.push({
                    label: `${name} (${test_status})`,
                    value: id,
                });
                return acc;
            }, []);
        }
    });

    async function handleSubmit() {
        if (model.validateForm() === false) {
            model = model;
            return false;
        }
        isLoading = true;
        let action = isNewRecord ? 'created' : 'updated';
        if (await model.save() === true) {
            alert.success(`🎉 ${group.name}: ${model.attributes.name} ${action} successfully!`);
            if (createAnother) {
                location.href = `/issue/create/${group.id}?createAnother`;
            } else {
                goto(`/issue/${model.get('id')}`);
            }
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

    function handleJiraUrlFocus() {
        if (!model.get('jira_url') && model.get('jira_number')) {
            model.set('jira_url', `${JIRA_WEB_URL}/browse/${model.get('jira_number')}`);
        }
    }

</script>

<form on:submit|preventDefault={handleSubmit}>

    <input type="hidden" name="group_id" value={group.id}>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('name')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="Issue name"
                    attribute="name"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    {#if !isNewRecord}
        <div class="mb-6">
            <DetailedRow paddingTop="0" paddingBottom="0">
                <div slot="label">
                    {model.getLabel('group_id')}
                </div>
                <div slot="content">
                    <ActiveAutoComplete 
                        {model}
                        options={groupOptions}
                        label={false}
                        required={true}
                        attribute="group_id"
                    />
                </div>
            </DetailedRow>
        </div>
    {/if}

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('status')}
            </div>
            <div slot="content">
                <ActiveDropdown 
                    {model}
                    attribute="status" 
                    label={false}
                    options={model.statusOptions}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('qa_user_id')}
            </div>
            <div slot="content">
                <ActiveAutoComplete 
                    {model}
                    placeholder="Optional"
                    options={userOptions}
                    label={false}
                    attribute="qa_user_id"
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('developer_user_id')}
            </div>
            <div slot="content">
                <ActiveAutoComplete 
                    {model}
                    placeholder="Optional"
                    options={userOptions}
                    label={false}
                    attribute="developer_user_id"
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('jira_number')}
            </div>
            <div slot="content">
                
                <ActiveTextInput 
                    {model}
                    attribute="jira_number"
                    label={false}
                    placeholder="WC-1234 (Optional)"
                />
                
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('jira_url')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    on:focus={handleJiraUrlFocus}
                    {model}
                    placeholder="Optional"
                    attribute="jira_url"
                    label={false}
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('lark_url')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="Optional"
                    attribute="lark_url"
                    label={false}
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('description')}
            </div>
            <div slot="content">
                <RichEditor 
                    {token}
                    {model}
                    attribute="description"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    {#if isNewRecord}
        <div>
            <Checkbox 
                checked={createAnother}
                on:change={(e) => { createAnother = e.detail.value }}
                name="create-another"
                label="Create Another?"
                marginBottom={5} 
                marginTop={6} />
        </div>
    {/if}

    <div class="">
        <Button 
            label="{isNewRecord ? 'Save' : 'Update'}" 
            type="submit" 
            color="{isNewRecord ? 'green' : 'blue'}"
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

