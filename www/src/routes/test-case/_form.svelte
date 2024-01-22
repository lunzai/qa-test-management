<script>
    import { onMount } from 'svelte';
    import { goto, stores } from '@sapper/app'
    import * as alert from '../../components/alert/alert.js';
    import ActiveTextInput from '../../components/input/ActiveText.svelte';
    import ActiveDropdown from '../../components/input/ActiveDropdown.svelte';
    import DetailedRow from '../../components/DetailedRow.svelte';
    import Button from '../../components/Button.svelte';
    import RichEditor from '../../components/input/RichEditor.svelte';
    import Checkbox from '../../components/input/Checkbox.svelte';
    import * as UserApi from '@app/api/user';

    export let model;
    export let issue;

    const { session, page } = stores();
    const { token } = $session;
    const { query } = $page;
    let isLoading = false;
    let createAnother = query.createAnother === undefined ? false : true;

    async function handleSubmit() {
        if (model.validateForm() === false) {
            model = model;
            return false;
        }
        isLoading = true;
        let isNewRecord = model.isNewRecord();
        let action = isNewRecord ? 'created' : 'updated';
        if (await model.save() === true) {
            alert.success(`🎉 ${issue.name}: ${model.get('short_description')} ${action} successfully!`);
            if (createAnother) {
                location.href = `/test-case/create/${issue.id}?createAnother`;
            } else {
                goto(`/issue/${issue.id}?tab=${model.getId()}`);
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

</script>

<form on:submit|preventDefault={handleSubmit}>

    <input type="hidden" name="issue_id" value={issue.id}>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('short_description')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="Keep it short ok?"
                    attribute="short_description"
                    label={false}
                    required 
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
                {model.getLabel('pre_condition')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="pre_condition"
                    label={false}
                    placeholder="Conditions that must be in place before testing can start"
                />
            </div>
        </DetailedRow>
    </div>

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
                {model.getLabel('description')}
            </div>
            <div slot="content">
                <RichEditor 
                    {token}
                    {model}
                    attribute="description"
                    label={false}
                    placeholder="Describe what you want to test"
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('replicate_step')}
            </div>
            <div slot="content">
                <RichEditor 
                    {token}
                    {model}
                    attribute="replicate_step"
                    label={false}
                    placeholder="Steps to replicate"
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('expected_result')}
            </div>
            <div slot="content">
                <RichEditor 
                    {token}
                    {model}
                    attribute="expected_result"
                    label={false}
                    placeholder="Expected result"
                />
            </div>
        </DetailedRow>
    </div>

    {#if model.isNewRecord()}
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
