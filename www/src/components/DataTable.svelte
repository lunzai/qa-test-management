<script>
    import { createEventDispatcher } from 'svelte';
    import Pagination from './Pagination.svelte';
    
    const dispatch = createEventDispatcher();

    export let actions;
    export let attributes;
    export let page;
    export let sortAttribute;
    export let sortDirection;
    export let data;
    export let activeFilter = {};
    export let perPage;

    let start;
    let end;
    let totalCount;
    let pageCount;
    
    $: hasFilterable = attributes.some(row => row.filterable === true);

    $: items = data.items;
    $: meta = data._meta;
    $: pageCount = meta ? meta.pageCount : null;
    $: totalCount = meta ? meta.totalCount : null;
    $: perPage = meta ? meta.perPage : null;
    
    $: page && totalCount && perPage && setSummary();

    $: attributes = attributes.map(row => {
        if (row.attribute == sortAttribute) {
            row.sort = sortDirection;
        } else {
            row.sort = '';
        }
        return row;
    });

    $: {
        attributes.forEach(row => {
            if (row.filterable) {
                let attr = row.attribute;
                if (activeFilter[attr] == undefined) {
                    activeFilter[attr] = '';
                }
            }
        });
    }

    function setSummary() {
        start = (page - 1) * perPage + 1;
        end = start + perPage - 1;
        if (end > totalCount) {
            end = totalCount;
        }
    }

    function changeFilter(event) {
        activeFilter[event.target.name] = event.target.value;
        dispatch('changeFilter', activeFilter);
    }

    function changeSort(attribute, direction) {
        direction = direction == 'desc' ? 'asc' : 'desc'
        dispatch('changeSort', { attribute, direction });
    }

    function prefetchSort(attribute, direction) {
        direction = direction == 'desc' ? 'asc' : 'desc'
        dispatch('prefetchSort', { attribute, direction });
    }

    function changePage(event) {
        dispatch('changePage', event.detail);
    }

    function prefetchPage(event) {
        dispatch('prefetchPage', event.detail);
    }

    

</script>

<style>
    table { border-spacing: 0 1.5rem; }
    tbody tr, div.noresult { box-shadow: rgba(0,0,0,.05) 0 4px 20px 0 }
    th.sortable { padding-left: 1.5rem }
    th.sortable::before { 
        content: '\f0dc';
        font-family: 'Font Awesome 5 Free';
        color: #ddd;
        position: absolute;
        left: 0.6rem;
    }
    th.sort-desc::after { 
        content: '\f0dd';
        font-family: 'Font Awesome 5 Free';
        color: #666;
        position: absolute;
        left: 0.6rem;
    }
    th.sort-asc::after { 
        content: '\f0de';
        font-family: 'Font Awesome 5 Free';
        color: #666;
        position: absolute;
        left: 0.6rem;
    }
    input::placeholder { 
        color: #cbd5e0;
        transition-property: all;  
        transition-duration: 200ms;  
    }
    input:focus::placeholder { padding-left: 5px; }
    select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background:url('/images/chevron-down.svg');
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="rgb(160,174,192)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>');
        background-position: calc(100%) 6px,calc(100% - 20px) 13px,100% 0;
        background-size: 12px 12px,10px 10px;
        background-repeat: no-repeat;
    }
</style>

<div data-cy="data-table-wrapper">
    {#if items}
        {#if totalCount} 
            <div data-cy="data-table-summary" class="text-gray-700 text-sm">
                Showing {start} - {end} of {totalCount} result(s) 
            </div>
        {/if}
        <table data-cy="data-table" class="w-full clear-both table border-separate table-auto">
            <thead class="align-middle">
                {#if attributes}
                    <tr class="table-row align-middle font-semibold tracking-wide uppercase text-left text-sm">
                        {#each attributes as { label, attribute, sortable, sort }, index}
                            <th 
                                class:sortable={sortable}
                                class="text-left align-middle px-5 relative {sort ? `sort-${sort}`:''}"
                            >
                                {#if sortable && attribute}
                                    <button 
                                        data-cy="data-table-sort"
                                        on:click={changeSort(attribute, sort)} 
                                        on:mouseenter={prefetchSort(attribute, sort)} 
                                        class="font-semibold tracking-wide uppercase text-sm focus:outline-none"
                                    >
                                        {label}
                                    </button>
                                {:else}
                                    {label}
                                {/if}                            
                            </th>
                        {/each}
                        {#if actions}
                            <th class="text-left align-middle px-5 relative">
                                {actions.length > 1 ? 'Actions' : 'Action'}
                            </th>
                        {/if}
                    </tr>
                    {#if hasFilterable}
                        <tr class="table-row align-middle font-semibold tracking-wide uppercase text-left text-sm">
                            {#each attributes as { attribute, filterable, filter, filterPlaceholder }, index} 
                                <th class="filterable px-5 text-left align-middle" class:w-20={attribute == 'id'}>
                                    {#if filterable}
                                        {#if Array.isArray(filter)}
                                            <select 
                                                data-cy="data-table-filter"
                                                on:change={changeFilter}
                                                value={activeFilter[attribute]}
                                                name={attribute}
                                                class="
                                                    pb-2 block w-full focus:outline-none text-gray-500 border-gray-500
                                                    appearance-none pr-6 relative border-b border-solid border-gray-500
                                                    rounded-none focus:border-indigo-500 transform transition-all 
                                                    duration-300
                                                "
                                            >
                                                <option value="">{filterPlaceholder ? filterPlaceholder : '-'}</option>
                                                {#each filter as { label, value }, index}
                                                    <option {value}>{label}</option>
                                                {/each}
                                            </select>
                                        {:else}
                                            <input 
                                                data-cy="data-table-filter"
                                                on:change={changeFilter}
                                                value={activeFilter[attribute]}
                                                name={attribute}
                                                class="
                                                    focus:outline-none pb-2 bg-transparent border-b border-solid 
                                                    border-gray-500 focus:border-indigo-500 text-gray-500 block 
                                                    w-full transform transition-all duration-300
                                                "
                                                type="text" 
                                                placeholder="{filterPlaceholder ? filterPlaceholder : ''}" 
                                            />
                                        {/if}
                                    {/if}
                                </th>
                            {/each}
                            {#if actions}
                                <th></th>
                            {/if}
                        </tr>
                    {/if}
                {/if}
            </thead>
            <tbody class="align-middle">
                {#if attributes && items}
                    {#each items as row, rowIndex (row.id)}
                        <tr class="bg-white transition-all duration-300 rounded-lg align-middle text-left transform hover:-translate-y-1">
                            {#each attributes as { attribute, label, value, component, componentParams }, attributeIndex (`${row.id}-${attribute}`)}
                                <td class="y-5 px-5 first:rounded-tl-lg first:rounded-bl-lg last:rounded-tr-lg last:rounded-br-lg text-left align-middle">
                                    {#if component && componentParams}
                                        <svelte:component this={component} { ...componentParams(row) } />
                                    {:else if typeof value === 'function'} 
                                        {value(row)} <!-- #TODO: RETHINK THIS -->
                                    {:else if attribute}
                                        {row[attribute] !== null ? row[attribute] : '-'}
                                    {:else}
                                        {value !== null ? value : '-'}
                                    {/if}                            
                                </td>
                            {/each}
                            {#if actions}
                                <td class="py-4 px-5 first:rounded-tl-lg first:rounded-bl-lg last:rounded-tr-lg last:rounded-br-lg text-left align-middle">
                                    {#each actions as { label, href, color, click, icon }, actionIndex}
                                        <a
                                            data-cy="data-table-action"
                                            rel=prefetch
                                            on:click={click ? dispatch(click, { ...row }) : false}
                                            href={typeof href === 'function' ? href(row) : href}
                                            class="text-{color}-400 hover:text-{color}-600 text-xs py-1 px-2 mr-1 last:mr-0 rounded border-{color}-400 hover:border-{color}-600 border transition-all duration-300 cursor-pointer"
                                        >{#if icon}<i class={icon}></i>{/if}{label ? label : ''}</a>
                                    {/each}
                                </td>
                            {/if}
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    {/if}
    {#if items.length == 0} 
        <div data-cy="data-table-empty" class="noresult text-gray-800 p-5 bg-white rounded-lg shadow-lg">❗️No result 😭😱🥺</div>
    {/if}
</div>

<div data-cy="data-table-pagination" class="mt-4">
    <Pagination 
        on:prefetch={prefetchPage}
        on:changePage={changePage}
        bind:page={page} 
        bind:pageCount={pageCount}
    />
</div>
