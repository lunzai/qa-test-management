<script>
    import { slide } from 'svelte/transition';
    export let isOpen = false;
    export let active = true;
    export let id;
</script>

<style>
    .collapse {
        box-shadow: 0 0 8px 0 rgba(0,0,0,.1);
    }
    .collapse-header::after {

        content: '\f0dd';
        font-family: 'Font Awesome 5 Free';
        position: absolute;
        right: 1rem;
        -webkit-transition: all .2s linear 0s;
        transition: all .2s linear 0s;
        color: #ccc;
        font-size: 0.8rem;
        margin-top: -2px;
    }
    .open .collapse-header::after {
        -webkit-transform: rotate(-180deg);
        -ms-transform: rotate(-180deg);
        transform: rotate(-180deg);
    }
</style>

<div 
    data-cy="collapse"
    class="collapse relative rounded-md mb-4 last:mb-0"
    {id}
    class:open={isOpen} 
    class:bg-gray-100={!active}
    class:opacity-50={!active}
>
    <div 
        data-cy="collapse-header"
        on:click={() => isOpen = !isOpen}
        class="collapse-header flex items-end border-b border-gray-200 py-2 pl-3 pr-10 cursor-pointer"
    >
        <slot name="header"></slot>
    </div>
    {#if isOpen}
        <div 
            data-cy="collapse-body"
            transition:slide|local={{ duration: 200 }}
            class="collapse-body py-2 px-3"
        >
            <slot name="body"></slot>
        </div>
    {/if}
</div>