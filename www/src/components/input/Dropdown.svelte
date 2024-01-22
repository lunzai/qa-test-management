<script>
    import { createEventDispatcher } from 'svelte'

    let dispatch = createEventDispatcher();

    export let label;
    export let options;
    export let error;
    export let required = false;
    export let name;
    export let value;
    
    export let inputClass;
    export let labelClass;
    export let errorClass;
    export let containerClass;

    const onChange = (event) => dispatch('change', { type: 'change', value: event.target.value, name, label });
    const onFocus = (event) => dispatch('focus', { type: 'focus', value: event.target.value, name, label });
    const onBlur = (event) => dispatch('blur', { type: 'blur', value: event.target.value, name, label });
</script>

<style>
    select {
        appearance: none;
        -moz-appearance: none;
        -webkit-appearance: none;
        background:url('/images/chevron-down.svg');
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="rgb(160,174,192)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>');
        background-position: calc(100% - 12px) 16px, center;
        background-size: 16px 16px,10px 10px;
        background-repeat: no-repeat;
    }
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
    <select 
        data-cy="select-{name}"
        on:change={onChange}
        on:focus={onFocus}
        on:blue={onBlur}

        bind:value={value} 
        class:border-red-500={error}
        {required} 
        id="input-{name}"
        {name}
        class="
            appearance-none block border rounded-md border-gray-300 w-full p-3
            text-base text-gray-700 leading-tight focus:outline-none focus:shadow-lg 
            focus:border-indigo-500 transition-all duration-200 bg-white
            {inputClass ? inputClass:''}
        "
    >
        <option data-cy="select-{name}-option" value="">-</option>
        {#each options as { value, label }, i}
            <option value={value}>{label}</option>
        {/each}
    </select>
    {#if error}
        <p 
            data-cy="select-{name}-error" 
            class="text-red-500 text-sm mt-1 {errorClass ? errorClass:''}"
        >{error}</p>
    {/if}
</div>