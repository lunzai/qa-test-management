<script>
    import Input from './Text.svelte';
    import { createEventDispatcher } from 'svelte'

    let dispatch = createEventDispatcher();

    export let model;
    export let attribute;
    export let label;

    if (label === undefined) {
        label = model.labels[attribute];
    }

    export let required = false;
    export let minlength;
    export let maxlength;
    export let placeholder;
    export let inputClass;
    export let labelClass;
    export let errorClass;
    export let containerClass;

    const onChange = (event) => dispatch('change', event.detail);
    const onFocus = (event) => dispatch('focus', event.detail);
    const onBlur = (event) => dispatch('blur', event.detail);
    const onKeyup = (event) => { 
        model.validateField(attribute);
        model = model;
        dispatch('keyup', event.detail);
    };
</script>

<Input 
    on:change={onChange}
    on:focus={onFocus}
    on:blue={onBlur}
    on:keyup={onKeyup}
    
    bind:value={model.attributes[attribute]}
    {label}
    error={model.errors[attribute]}
    name={attribute}

    {required}
    {minlength}
    {maxlength}    
    {placeholder}
    {inputClass}
    {labelClass}
    {errorClass}
    {containerClass}
/>