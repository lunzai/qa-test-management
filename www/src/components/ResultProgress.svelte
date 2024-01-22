<script>
    import { tweened } from 'svelte/motion';
    import { cubicOut } from 'svelte/easing';
    import { onMount } from 'svelte';
    import Bar from './ResultProgressBar.svelte';

    export let items;
    const total = items.reduce((total, row) => total + row.count, 0);
</script>

{#if total > 0}
    <div data-cy="progress-bar" class="rounded mt-2 text-xs bg-gray-100 flex items-stretch justify-start overflow-hidden whitespace-no-wrap">
        {#each items as item}
            <Bar 
                {...item}
                {total}
            />
        {/each}
    </div>
{/if}