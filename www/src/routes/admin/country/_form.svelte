<script>
    import { goto, stores } from '@sapper/app'
    import * as alert from '../../../components/alert/alert.js';
    import ActiveTextInput from '../../../components/input/ActiveText.svelte';
    import ActiveDropdown from '../../../components/input/ActiveDropdown.svelte';
    import DetailedRow from '../../../components/DetailedRow.svelte';
    import Button from '../../../components/Button.svelte';
    import validate from 'validate.js';

    export let model;

    const baseUrl = '/admin/country';
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
            alert.success(`🎉 ${model.get('name')} ${action} successfully!`);
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
                {model.getLabel('iso2')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="iso2"
                    label={false}
                    minlength={2}
                    maxlength={2}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('iso3')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="iso3"
                    label={false}
                    minlength={3}
                    maxlength={3}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="">
        <Button 
            size="sm"
            label="{model.isNewRecord() ? 'Save' : 'Update'}" 
            type="submit" 
            color="{model.isNewRecord() ? 'green' : 'blue'}"
            isLoading={isLoading} 
            disabled={model.hasErrors()}
        />

        <Button 
            size="sm"
            label="Cancel"
            color="gray"
            href={model.isNewRecord() ? `${baseUrl}` : `${baseUrl}/${model.getId()}`}
            isLink={true}
        />

    </div>
</form>

