<script>
    import { fade } from 'svelte/transition';
    import { tweened } from 'svelte/motion';
    import { cubicOut } from 'svelte/easing';
    import { onMount } from 'svelte';

    export let color;
    export let label;
    export let count;
    export let total;

    let percent = count / total * 100;
    let show = false;

    const progress = tweened(0, {
        duration: 500,
        easing: cubicOut
    });   

    onMount(() => {
        progress.set(percent).then(() => show = true);
    });
</script>

<div 
    data-cy="progress-bar-item"
    class="bg-{color}-400 text-white tracking-wide text-center py-px overflow-hidden h-5" 
    style="width: {$progress}%"
>
    {#if show}
    <small transition:fade>
        <span class="hidden sm:inline-block">{label} ({count})</span>
        <span class="sm:hidden">{count}</span>
    </small>
    {/if}
</div>