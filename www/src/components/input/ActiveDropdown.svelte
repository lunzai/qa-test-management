<script>
    import Dropdown from './Dropdown.svelte';
    import { createEventDispatcher } from 'svelte'

    let dispatch = createEventDispatcher();

    export let model;
    export let attribute;
    export let label;
    export let options = [];
    export let required = false;

    export let inputClass;
    export let labelClass;
    export let errorClass;
    export let containerClass;

    if (label === undefined) {
        label = model.getLabel(attribute);
    }

    const onChange = (event) => {
        model.set(attribute, event.detail.value);
        model.validateField(attribute);
        model = model;
        dispatch('change', event.detail);
    };
    const onFocus = (event) => dispatch('focus', event.detail);
    const onBlur = (event) => dispatch('blur', event.detail);
</script>

<Dropdown 
    on:change={onChange}
    on:focus={onFocus}
    on:blue={onBlur}

    value={model.get(attribute)}
    {label}
    error={model.getError(attribute)}
    name={attribute}

    {options}
    {required} 
    {inputClass}
    {labelClass}
    {errorClass}
    {containerClass}
/>