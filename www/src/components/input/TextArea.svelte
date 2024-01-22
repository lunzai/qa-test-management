<script>
    import { createEventDispatcher } from 'svelte'

    let dispatch = createEventDispatcher();

    export let label;
    export let value;
    export let error;
    export let required = false;
    
    export let name;
    export let placeholder;

    export let inputClass;
    export let labelClass;
    export let errorClass;
    export let containerClass;

    const onChange = (event) => dispatch('change', { type: 'change', value: event.target.value, name, label });
    const onFocus = (event) => dispatch('focus', { type: 'focus', value: event.target.value, name, label });
    const onBlur = (event) => dispatch('blur', { type: 'blur', value: event.target.value, name, label });
    const onKeyup = (event) => dispatch('keyup', { type: 'keyup', value: event.target.value, name, label });
</script>

<style>
    textarea { min-height: 12rem; }
    textarea::placeholder { 
        color: #a0aec0;
        transition-property: all;  
        transition-duration: 200ms;  
    }
    textarea:focus::placeholder { padding-left: 5px; }
</style>

<div class="{containerClass}">
    {#if label}
        <label 
            data-cy="label-{name}" 
            class="block text-gray-700 tracking-wide font-medium mb-2 {labelClass ? labelClass:''}" for="{name}"
        >
            {label}
        </label>
    {/if}
    <textarea 
        data-cy="textarea-{name}" 
        on:change={onChange}
        on:focus={onFocus}
        on:blue={onBlur}
        on:keyup={onKeyup}

        bind:value={value} 
        class:border-red-500={error}
        {required} 
        id="input-{name}"
        {name}
        {placeholder} 
        class="appearance-none block border rounded-md border-gray-300 w-full p-3
        text-base text-gray-700 leading-tight focus:outline-none focus:shadow-lg 
        focus:border-indigo-500 transition-all duration-200
        {inputClass ? inputClass:''}"
        />
    {#if error}
        <p 
            data-cy="textarea-{name}-error" 
            class="text-red-500 text-sm mt-1 {errorClass ? errorClass:''}"
        >{error}</p>
    {/if}
</div>