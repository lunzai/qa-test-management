<script>
    import Container from './Container.svelte';
    export let breadcrumb = [];
    export let title;
    const numberOfCrumbs = breadcrumb.length;
</script>

<style>
    .breadcrumb a, .breadcrumb span { max-width: 300px; }
</style>

{#if breadcrumb || title}
    <Container class="py-6">
        {#if breadcrumb}
            <ul data-cy="breadcrumb" class="hidden sm:flex mb-2 breadcrumb">
                {#each breadcrumb as { label, href }, i}
                    <li class="mx-2 ml-0 text-gray-600 flex">
                        {#if href}
                            <a class="hover:text-gray-700w truncate" rel="prefetch" {href}>{label}</a>
                        {:else}
                            <span class="truncate">{label}</span>
                        {/if}
                        {#if i < numberOfCrumbs - 1}                            
                            <span class="ml-2 text-gray-400">&rsaquo;</span>
                        {/if}
                    </li>
                {/each}
            </ul>
        {/if}
        <div data-cy="page-title" class="flex items-center">
            {#if title}
                <h1 class="tracking-wide text-gray-700 font-medium text-3xl flex-grow truncate">{title}</h1>
            {/if}
            <slot></slot>
        </div>        
    </Container>
{/if}
