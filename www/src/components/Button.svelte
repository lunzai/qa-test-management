<script>
    import { createEventDispatcher } from 'svelte';
    const dispatch = createEventDispatcher();
    
    export let label;
    export let size = 'md';
    export let type = 'submit';
    export let color = 'indigo';
    export let icon;
    export let href = null;
    export let isLight = true;
    export let isLoading = false;
    export let isLink = false;
    export let isDisabled = false;

    export let buttonClass;
    export let hoverButtonClass = `hover:shadow-xl`;
    export let disabledClass = `opacity-50 cursor-not-allowed`;
    
    let paddingClass;

    switch (size) {
        case 'sm':
            paddingClass = `py-2 px-6`;
            break;
        case 'md':
        default: 
            paddingClass = `py-3 px-8`;
            break;
    }

    const onClick = (event) => dispatch('click', { type: 'click', type, label });

    // TODO: More button / a events
</script>

<style>
    .btn-is-loading {
        padding-right: 40px !important;
    }
    .btn-is-loading:before {
        content: '';
        box-sizing: border-box;
        position: absolute;
        top: 50%;
        right: 10px;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        border-radius: 999px;
        border-top: 2px solid #fff;
        border-right: 2px solid transparent;
        border-bottom: 2px solid transparent;
        animation: spinner .65s linear infinite;        
    }
    @keyframes spinner {
        to {transform: rotate(360deg);}
    }
</style>

{#if isLink}
    <a 
        rel=prefetch
        on:click={onClick}
        disabled={isDisabled}
        class="
            bg-{color}-500 inline-block relative font-medium
            rounded-md focus:outline-none transition-all duration-200
            {paddingClass}
            {buttonClass ? buttonClass : ''}
            {isDisabled || isLoading ? disabledClass : hoverButtonClass}
            {isLight ? `text-white` : `text-black`}
            {isLoading ? `btn-is-loading` : ``}
        "
        {href}>
        {#if icon}<i class="{icon}"></i>{/if}
        {label}
    </a>
{:else}
    <button 
        on:click={onClick}
        disabled={isDisabled || isLoading}
        class="
            bg-{color}-500 relative text-white font-medium 
            rounded-md focus:outline-none transition-all duration-200 
            {paddingClass}
            {buttonClass ? buttonClass : ''}
            {isDisabled  || isLoading ? disabledClass : hoverButtonClass}
            {isLight ? `text-white` : `text-black`}
            {isLoading ? `btn-is-loading` : ``}
        "
        {type}>
        {#if icon}<i class="{icon}"></i>{/if}
        {label ? label : ''}
    </button>
{/if}

