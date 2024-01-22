<script>
    import { createEventDispatcher } from 'svelte'
    let dispatch = createEventDispatcher();

    export let checked = false;
    export let icon = 'fas fa-check';
    export let color = 'indigo';
    export let label = false;
    export let marginTop = 0;
    export let marginBottom = 0;
    export let name;

    const onChange = (event) => dispatch('change', { type: 'change', value: checked, name, label });
</script>

<style>
    .checked .show-checkbox-check {
        -webkit-transform: rotate(0);
        -ms-transform: rotate(0);
        transform: rotate(0);
    }
    .checked .show-checkbox {
        -webkit-transform: translate(0);
        -ms-transform: translate(0);
        transform: translate(0);
    }
</style>

<div class="inline-block mt-{marginTop} mb-{marginBottom}">
    <div 
        class:checked={checked}
        class="checkbox relative flex items-center justify-start"
    >
        <input 
            bind:checked={checked}
            on:change={onChange}
            class="absolute h-full w-full opacity-0 z-20 cursor-pointer 
            top-0 p-0 m-0"
            type="checkbox" 
            name="" />
        <span 
            class="show-checkbox cursor-pointer relative w-6 h-6 border-2 border-gray-600 
            rounded-sm transform -rotate-90 transition-all overflow-hidden 
            mr-3 duration-200 {checked ? `border-${color}-500` : ''}">
            <span 
                class="show-checkbox-check w-full h-full absolute left-0 transform 
                translate-x-full origin-right transition-all duration-200 z-10 
                flex justify-center items-center
                {checked ? `bg-${color}-500` : ''}"
            >
                <i 
                    class:opacity-100={checked}
                    class:leading-normal={checked}
                    class="{icon} text-white text-base"></i>
            </span>
        </span>
        {#if label}
            <span>{label}</span>
        {/if}
    </div>
</div>