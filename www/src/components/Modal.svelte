<script>
    import { fly, fade } from 'svelte/transition';
    import { quintOut } from 'svelte/easing';
    import Wrapper from './Wrapper.svelte';
    import Card from './Card.svelte';

    export let visible = false;
    export let size = '2xl';
    export let title;
    export let subtitle;
    export let id;

    function handleKeydown(event) {
        if (event.keyCode == 27 && visible == true) {
            visible = false;
        }
    }
</script>

<svelte:window on:keydown={handleKeydown} />

{#if visible}
    <div 
        {id}
        data-cy="modal-screen"
        class="fixed inset-0 flex items-center justify-center z-20"
    >
        <div 
            data-cy="modal-close"
            transition:fade={{ duration: 300, easing: quintOut }}
            class:hidden={!visible}
            class=""
        >
            <div on:click={() => { visible = false; }} class="fixed inset-0 bg-black opacity-80"></div>
        </div>

        <div data-cy="modal-content" class="max-w-{size} w-full z-10" transition:fly>
            <Card {title} {subtitle}>
                <Wrapper>
                    <slot></slot>
                </Wrapper>
            </Card>
        </div>

    </div>
{/if}