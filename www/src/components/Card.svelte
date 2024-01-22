<script>
    import Wrapper from './Wrapper.svelte';

    export let title;
    export let subtitle; 
</script>

<div data-cy="card" class="rounded-lg shadow-lg overflow-hidden bg-white">
    {#if title || subtitle}
        <Wrapper >
            {#if title}
                <h4 class="text-xl leading-tight font-medium text-gray-900 truncate">
                    {title}
                </h4>
            {/if}
            {#if subtitle}
                <h6 class="mt-1 leading-right text-gray-500">
                    {subtitle}
                </h6>
            {/if}
        </Wrapper>
    {/if}

    <slot></slot>
    
</div>