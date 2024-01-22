<script>
    import { createEventDispatcher } from 'svelte';

    export let attributes;
    export let data;
    export let actions;
    export let enableInactiveRow = false;
    export let isInactiveRow;
    
    const dispatch = createEventDispatcher();

</script>

<style>
    table { border-spacing: 0 1.5rem; }
    tbody tr, div.noresult { box-shadow: rgba(0,0,0,.05) 0 4px 20px 0 }
</style>

<div data-cy="simple-table" class="overflow-x-auto">
    {#if data.length}
        <table class="w-full clear-both table border-separate table-auto">
            <thead class="font-semibold tracking-wide uppercase text-sm text-left">
                <tr>
                    {#if attributes}
                        {#each attributes as { label }, index}
                            <th class="px-5">
                                {label}
                            </th>
                        {/each}
                    {/if}
                </tr>
            </thead>
            <tbody class="align-middle">
                {#if attributes}
                    {#each data as row, rowIndex (row.id)}
                        <tr 
                            class="bg-white transition-all duration-300 rounded-lg align-middle text-left transform hover:-translate-y-1"
                            class:opacity-40={enableInactiveRow && isInactiveRow(row)}
                        >
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
                                            rel="prefetch"
                                            on:click={click ? dispatch(click, { ...row }) : false}
                                            href={typeof href === 'function' ? href(row) : href}
                                            class="text-{color}-400 hover:text-{color}-600 text-xs py-1 px-2 mr-1 last:mr-0 rounded border-{color}-400 hover:border-{color}-600 border transition-all duration-300 cursor-pointer"
                                        >{#if icon}<i class={icon}></i>{/if}{label ? ` ${label}` : ''}</a>
                                    {/each}
                                </td>
                            {/if}
                        </tr>
                    {/each}
                {/if}
            </tbody>
        </table>
    {:else}
        <div class="noresult text-gray-800 p-5 bg-white rounded-lg shadow-lg">❗️No result 😭😱🥺</div>
    {/if}
</div>
