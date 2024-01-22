<script>
    import { createEventDispatcher } from 'svelte';

	const dispatch = createEventDispatcher();

    export let page = 1;
    export let pageCount = 1;

    const numberOfButton = 5;
    const leftPadding = Math.floor((numberOfButton - 1) / 2);
    const rightPadding = Math.ceil((numberOfButton - 1) / 2); 
    let pages = [];
    let start;
    let end;

    $: {
        page = parseInt(page, 10);
        pages = [];
        if (pageCount <= numberOfButton) {
            start = 1;
            end = pageCount;
        } else if ((page + rightPadding) >= pageCount) {
            start = pageCount - numberOfButton + 1;
            end = pageCount;
        } else if ((page - leftPadding) <= 1) {
            start = 1;
            end = numberOfButton;
        } else {
            start = page - leftPadding;
            end = page + rightPadding;
            if (start < 1) {
                start = 1;
            }
            if (end > pageCount) {
                end = pageCount;
            }
        }
    
        if (pageCount > numberOfButton) {
            pages.push({
                type: 'first',
                label: 'First',
                page: 1,
                active: false,
                disabled: page <= 1,
            });
        }
    
        pages.push({
            type: 'prev',
            label: 'Prev',
            page: (page - 1) >= 1 ? page - 1 : 1,
            active: false,
            disabled: page <= 1,
        });
    
        for (let i = start; i <= end; i++) {
            pages.push({
                type: 'item',
                label: i,
                page: i,
                active: page == i,
                disabled: false,
            });
        }
    
        pages.push({
            type: 'next',
            label: 'Next',
            page: (page + 1) <= pageCount ? page + 1 : pageCount,
            active: false,
            disabled: page >= pageCount,
        });
    
        if (pageCount > numberOfButton) {
            pages.push({
                type: 'last',
                label: 'Last',
                page: pageCount,
                active: false,
                disabled: page >= pageCount,
            });
        }
    }

    function changePage(gotoPage) {
        if (page != gotoPage) {
            // dispatch some event
            dispatch('changePage', gotoPage);
        }
    }

    function prefetch(gotoPage) {
        if (page != gotoPage) {
            // dispatch some event
            dispatch('prefetch', gotoPage);
        }
    }
</script>

<style>
    li { 
        backface-visibility: visible; 
        -webkit-backface-visibility: visible 
    }
    li.active { color: #fff; }
    li.disabled { color: gray; }
    .effect { content: "" }
    li.active .effect {
        transform: scale(1.1);
        -webkit-transform: scale(1.1);
        opacity: 1;
    }
    li.page-prev::before {
        font-family: 'FontAwesome';
        content: '\f104';
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }
    li.page-next::after {
        font-family: 'FontAwesome';
        content: '\f105';
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }
</style>

{#if pageCount > 1}
    <div data-cy="pagination" class="flex items-center justify-center">
        <ul class="flex items-center justify-center rounded-full bg-white px-5">
            {#each pages as { page, disabled, active, type, label }, i (`${type}-${page}`)}
                <li 
                    data-cy="pagination-{type}"
                    {disabled} 
                    page={page}
                    on:click={() => { changePage(page); }}
                    on:mouseenter={() => { prefetch(page); }}
                    class="
                        page-{type} text-gray-700 font-semibold focus:outline-none 
                        hover:text-indigo-500 transform transition-all duration-200 
                        relative flex items-center justify-center align-middle 
                        cursor-pointer mx-1
                    "
                    class:active={active}
                    class:disabled={disabled}
                    class:cursor-not-allowed={disabled}
                    class:h-10={type == 'item'}
                    class:w-10={type == 'item'}
                    class:rounded-full={type == 'first' || type == 'last' || active}
                    class:text-indigo-500={type == 'first' || type == 'last'}
                    class:ml-3={type == 'next' || type == 'last'}
                    class:mr-3={type == 'prev' || type == 'first'}
                    
                >
                        <span class="z-20">{label}</span>
                        <div class="effect opacity-0 transform scale-50 absolute z-10 h-full w-full rounded-full duration-200 transition-all top-0 bg-indigo-500"></div>
                </li>
            {/each}
        </ul> 
    </div>    
{/if}

