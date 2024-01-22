<script>
    import { stores } from '@sapper/app'
    import * as alert from '../../../components/alert/alert.js';
    import ActiveTextInput from '../../../components/input/ActiveText.svelte';
    import ActiveDropdown from '../../../components/input/ActiveDropdown.svelte';
    import DetailedRow from '../../../components/DetailedRow.svelte';
    import Button from '../../../components/Button.svelte';
    import validate from 'validate.js';
    import * as UserApi from '@app/api/user';

    export let model;

    const { session } = stores();
    const { token } = $session;
    let isLoading = false;
    
    async function handleSubmit() {
        if (model.isNewRecord()) {
            return false;
        }
        if (model.validateForm() === false) {
            model = model;
            return false;
        }
        isLoading = true;
        if (await model.save() === true) {
            alert.success(`🎉 ${model.get('display_name')}'s profile updated!`);
        } else if (!model.hasErrors()) {
            alert.danger(`❗️Unable to update. Some server error.`);
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
                {model.getLabel('email')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="whoever@whitecoat.global"
                    attribute="email"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('display_name')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="Who who who"
                    attribute="display_name"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>
    
    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('job_role')}
            </div>
            <div slot="content">
                <ActiveDropdown 
                    {model}
                    attribute="job_role" 
                    label={false}
                    options={model.jobRoleOptions}
                    required 
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
    <!--
    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('lark_id')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    placeholder="Lark User ID"
                    attribute="lark_id"
                    label={false} 
                />
            </div>
        </DetailedRow>
    </div>
    -->
    <div class="">
        <Button 
            label="Save" 
            type="submit" 
            color="green"
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
