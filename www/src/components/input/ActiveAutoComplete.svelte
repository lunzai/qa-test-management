<script>
    import AutoComplete from './AutoComplete.svelte';
    import { createEventDispatcher } from 'svelte'
    let dispatch = createEventDispatcher();

    export let model;
    export let attribute;
    export let label;
    export let placeholder;
    export let options = [];    
    export let required= false;

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
        console.log(model.getSafeAttributes());
        console.log(model);
        dispatch('change', event.detail);
    };
    const onFocus = (event) => dispatch('focus', event.detail);
    const onBlur = (event) => dispatch('blur', event.detail);

</script>

<AutoComplete 
    on:change={onChange}
    on:focus={onFocus}
    on:blue={onBlur}

    {label}
    {options}
    {attribute}
    value={model.get(attribute)}
    error={model.getError(attribute)}

    {containerClass}
    {inputClass}
    {labelClass}
    {errorClass}
    {placeholder}
    {required}  
/>