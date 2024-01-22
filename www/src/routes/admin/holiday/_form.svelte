<script>
    import { onMount } from 'svelte';
    import { goto, stores } from '@sapper/app'
    import * as alert from '../../../components/alert/alert.js';
    import ActiveTextInput from '../../../components/input/ActiveText.svelte';
    import ActiveDropdown from '../../../components/input/ActiveDropdown.svelte';
    import ActiveAutoComplete from '../../../components/input/ActiveAutoComplete.svelte';
    import DetailedRow from '../../../components/DetailedRow.svelte';
    import Button from '../../../components/Button.svelte';
    import * as CountryApi from '@app/api/country';
    import validate from 'validate.js';

    export let model;

    const { session } = stores();
    const { token } = $session;
    const baseUrl = '/admin/holiday';
    let isLoading = false;
    let countryOptions = [];


    onMount(async () => {
        const countries = await CountryApi.getCountryList(token);
        if (countries.success) {
            countryOptions = countries.data.items.reduce((acc, { id, name }) => {
                acc.push({
                    label: name,
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
        let isNewRecord = model.isNewRecord();
        let action = isNewRecord ? 'created' : 'updated';
        if (await model.save() === true) {
            alert.success(`🎉 ${model.get('title')} ${action} successfully!`);
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
                {model.getLabel('title')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="title"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('start')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="start"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('end')}
            </div>
            <div slot="content">
                <ActiveTextInput 
                    {model}
                    attribute="end"
                    label={false}
                    required 
                />
            </div>
        </DetailedRow>
    </div>

    <div class="mb-6">
        <DetailedRow paddingTop="0" paddingBottom="0">
            <div slot="label">
                {model.getLabel('country_id')}
            </div>
            <div slot="content">
                <ActiveAutoComplete 
                    {model}
                    options={countryOptions}
                    label={false}
                    required={true}
                    attribute="country_id"
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

