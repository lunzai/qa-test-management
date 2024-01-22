<script>
    import { goto, stores } from '@sapper/app'
    import * as alert from '../../components/alert/alert.js';
    import ActiveTextInput from '../../components/input/ActiveText.svelte';
    import ActiveDropdown from '../../components/input/ActiveDropdown.svelte';
    import DetailedRow from '../../components/DetailedRow.svelte';
    import Button from '../../components/Button.svelte';
    import validate from 'validate.js';

    export let model;

    const baseUrl = '/group';
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
            alert.success(`🎉 ${model.attributes.name} ${action} successfully!`);
            goto(`${baseUrl}/${model.getId()}`);
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
                {model.getLabel('name')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="name"
                    label={false}
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

