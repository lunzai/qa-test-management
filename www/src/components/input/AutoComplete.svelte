<script>
    import { fade } from 'svelte/transition';
    import { createEventDispatcher, onMount } from 'svelte'
    let dispatch = createEventDispatcher();

    export let label;
    export let options = [];
    export let error;
    export let isShow = false;
    export let value;
    export let name;
    export let required;
    export let placeholder;

    export let inputClass;
    export let labelClass;
    export let errorClass;
    export let containerClass;

    let inputValue;
    let isInitialLoaded = options.length > 0;
    $: filteredOptions = options;
    $: isActive = (isShow && filteredOptions.length > 0);
    
    $: {
        if (options.length > 0 && isInitialLoaded === false) {
            isInitialLoaded = true;
            let initialValue = options.find(row => row.value == value);
            if (initialValue) {
                inputValue = initialValue.label;
            }
        }
    }

    function handleKeyup() {
        isShow = true;
        value = null;
        dispatch('keyup', { 
            type: 'keyup', 
            value, 
            inputValue, 
            name, 
            label 
        });
        if (!inputValue || inputValue == '') {
            handleClear();
            // filteredOptions = options;
            return;
        } 
        let match = options.find(row => row.label.toLowerCase() == inputValue.toLowerCase());
        if (match !== undefined) {
            handleChange(match.label, match.value);
            return;
        }
        filteredOptions = options.filter(row => {
            return row.label.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1;
        });
        if (filteredOptions.length == 0 && value == null) {
            isShow = false;
            if (required) {
                error = `Invalid selection`;
            }
        }        
    }

    function handleChange(optionLabel, optionValue) {
        value = optionValue;
        inputValue = optionLabel;
        error = false;
        isShow = false;
        dispatch('change', { 
            type: 'change', 
            value, 
            inputValue, 
            name, 
            label 
        });
    }

    function handleFocus() {
        error = false;
        isShow = true;
        dispatch('focus', { 
            type: 'focus', 
            value, 
            inputValue, 
            name, 
            label 
        });
    }

    function handleBlur() {
        isShow = false;
        if (!value && (inputValue || required)) {
            error = `Invalid selection`;
        }
        dispatch('blur', { 
            type: 'blur', 
            value, 
            inputValue, 
            name, 
            label 
        });
    }

    function handleClear() {
        handleChange(null, null);
        filteredOptions = options;
    }
</script>

<style>
    .autocomplete-wrapper { max-height: 20rem; }
    input::placeholder { 
        color: #a0aec0;
        transition-property: all;  
        transition-duration: 200ms;  
    }
    input:focus::placeholder { padding-left: 5px; }
</style>


<div class="{containerClass}">
    {#if label}
        <label 
            data-cy="label-{name}"
            class="block text-gray-700 tracking-wide font-medium mb-2 
            {labelClass ? labelClass:''}" for="{name}"
        >
            {label}
        </label>
    {/if}
    <div 
        class="relative"
        class:z-20={isShow}
    >
        <input 
            data-cy="input-{name}"
            on:keyup={handleKeyup}
            on:focus={handleFocus}
            on:blur={handleBlur}
            bind:value={inputValue}
            type="text" 
            id="input-{name}"
            {name}
            {placeholder}
            class="appearance-none block border rounded-md border-gray-300 w-full p-3
            text-base text-gray-700 leading-tight focus:outline-none focus:shadow-lg 
            focus:border-indigo-500 transition-all duration-200 rounded-br-none rounded-bl-none
            pr-8
            {inputClass ? inputClass : ''}
            {isInitialLoaded ? '' : 'bg-gray-200 cursor-wait'}"
            class:border-red-500={error}
            class:rounded-br-none={isActive}
            class:rounded-bl-none={isActive}
            class:z-20={isActive}
            />
        {#if inputValue}
            <button 
                data-cy="input-{name}-clear"
                on:click={handleClear}
                class="button-close absolute h-full px-3 top-0 right-0 focus:outline-none"
            >
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    class="fill-current h-4 w-4 text-gray-500" 
                    viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        {/if}

        {#if isActive}
        <div 
            data-cy="input-{name}-result"
            class="autocomplete-wrapper absolute left-0 w-full flex flex-col items-stretch 
            bg-white shadow-lg rounded-br-sm rounded-bl-sm border-gray-300 border -mt-px
            overflow-y-scroll z-20"
            transition:fade={{ duration: 150 }}
        >
            {#each filteredOptions as { label, value }, i (`${label}-${value}`)}
                <button 
                    data-cy="input-{name}-result-row"
                    transition:fade={{ duration: 150 }}
                    on:click={() => handleChange(label, value)}
                    type="button"
                    class="relative py-4 px-5 focus:outline-none hover:text-indigo-500 
                    hover:bg-gray-100 text-left"
                >{label}</button>
            {/each}
        </div>
        {/if}
        {#if error}
            <p 
                data-cy="input-{name}-error" 
                class="text-red-500 text-sm mt-1 {errorClass ? errorClass:''}"
            >{error}</p>
        {/if}
    </div>
</div>