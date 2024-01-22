<script context="module">
    import { loginIsValid } from '@app/accessControl';
    import * as Api from '@app/api/bugFeature';
    import * as utils from '@app/utils';
    import {
        PRIORITY_LOW, PRIORITY_MEDIUM, PRIORITY_HIGH, PRIORITY_BLOCKER,
        STATUS_ACTIVE, STATUS_DELETED, STATUS_DEPLOYED,
        FIX_STATUS_PENDING_INVESTIGATION, FIX_STATUS_UNABLE_TO_REPLICATE,
        FIX_STATUS_NEED_MORE_INFORMATION, FIX_STATUS_WORK_IN_PROGRESS,
        FIX_STATUS_TO_DO, FIX_STATUS_FIXED, FIX_STATUS_KIV,
        TYPE_BUG, TYPE_FEATURE
    } from '@app/constants';

    export async function preload({ query }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        const perPage = 20;
        const filter = utils.objectFilter(query, ([key, value]) => (key != 'sort' && key != 'page'));
        const page = query.page ? parseInt(query.page, 10) : 1;
        const sort = query.sort ? query.sort : '-id';
        const sortAttribute = sort && sort[0] == '-' ? sort.substring(1) : sort;
        const sortDirection = sort && sort[0] == '-' ? 'desc' : 'asc';
        let data;
        try {
            const response = await Api.list({ 'per-page': perPage, page, sort, ...filter, ...{ type: TYPE_BUG } }, token);
            if (response.success) {
                data = response.data;
            } else {
                this.error(400, '😰 Unable to load data.');
            }            
        } catch (e) {
            console.error('Bug', e);
            alert.danger(e.message);
        }
        return { data, filter, page, sortAttribute, sortDirection, query };
    }
</script>

<script>
    import { goto } from '@sapper/app';
    import * as alert from '../../components/alert/alert.js';
    import DataTable from '../../components/DataTable.svelte';
    import PageTitle from '../../components/PageTitle.svelte';
    import Container from '../../components/Container.svelte';
    import Button from '../../components/Button.svelte';
    import Status from '../../components/Status.svelte';
    
    // TODO: move everything to active data table?
    export let query;
    export let data;
    export let filter;
    export let page;
    export let sortAttribute;
    export let sortDirection;
    
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Bug Report', href: '/bug' },
    ];
    
    async function redirect() {
        let urlQuery = utils.urlQuery(query);
        await goto(`/bug?${urlQuery}`);
    }

    // TODO: finish the prefetch impletement for pagination and sort
    function handleChangePage(event) {
        let goToPage = event.detail;
        query.page = parseInt(goToPage, 10);
        redirect();
    }

    function handleChangeSort(event) {
        let { attribute, direction } = event.detail;
        let sort = direction == 'asc' ? '' : '-';
        query.sort = `${sort}${attribute}`;
        redirect();
    }

    function handleChangeFilter(event) {
        query = { ...query, ...event.detail };
        query.page = 1; 
        redirect();
    }

    let datatableAttributes = [
        { 
            attribute: 'id',
            label: 'ID', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'ID',
        },
        { 
            attribute: 'title',
            label: 'Title', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'Title',
        },
        { 
            attribute: 'jira_number',
            label: 'JIRA', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'JIRA',
        },
        { 
            attribute: 'status',
            label: 'Status', 
            sortable: true,
            filterable: true,
            component: Status,
            componentParams: model => {
                return { status: model.status, label: model.status }  
            },
            filter: [
                { label: STATUS_ACTIVE, value: STATUS_ACTIVE },
                { label: STATUS_DELETED, value: STATUS_DELETED },
                { label: STATUS_DEPLOYED, value: STATUS_DEPLOYED },
            ],
        },
        { 
            attribute: 'fix_status',
            label: 'Fix Status', 
            sortable: true,
            filterable: true,
            component: Status,
            componentParams: model => {
                return { status: model.fix_status, label: model.fix_status }  
            },
            filter: [
                { value: FIX_STATUS_PENDING_INVESTIGATION, label: FIX_STATUS_PENDING_INVESTIGATION },
                { value: FIX_STATUS_UNABLE_TO_REPLICATE, label: FIX_STATUS_UNABLE_TO_REPLICATE },
                { value: FIX_STATUS_NEED_MORE_INFORMATION, label: FIX_STATUS_NEED_MORE_INFORMATION },
                { value: FIX_STATUS_WORK_IN_PROGRESS, label: FIX_STATUS_WORK_IN_PROGRESS },
                { value: FIX_STATUS_TO_DO, label: FIX_STATUS_TO_DO },
                { value: FIX_STATUS_FIXED, label: FIX_STATUS_FIXED },
                { value: FIX_STATUS_KIV, label: FIX_STATUS_KIV },
            ],
        },
        { 
            attribute: 'priority',
            label: 'Priority', 
            sortable: true,
            filterable: true,
            component: Status,
            componentParams: model => {
                return { status: model.priority, label: model.priority }  
            },
            filter: [
                { value: PRIORITY_LOW, label: PRIORITY_LOW },
                { value: PRIORITY_MEDIUM, label: PRIORITY_MEDIUM },
                { value: PRIORITY_HIGH, label: PRIORITY_HIGH },
                { value: PRIORITY_BLOCKER, label: PRIORITY_BLOCKER },
            ],
        }
    ];

    const datatableActions = [
        {
            label: null,
            icon: 'fa fa-folder-open',
            color: 'blue',
            href: row => `/bug/${row.id}`,
        },
        {
            label: null,
            icon: 'fa fa-pen',
            color: 'teal',
            href: row => `/bug/${row.id}/update`,
        },
    ];


</script>

<style>
    div.noresult { box-shadow: rgba(0,0,0,.05) 0 4px 20px 0 }
</style>

<svelte:head>
    <title>Bug Report</title>
</svelte:head>

<PageTitle title="Bug Report 🐛" {breadcrumb}>
    <div class="">
        <Button 
            size="sm"
            label="Create"
            color="green"
            href="/bug/create"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>

    {#if !data}
        <div class="noresult text-gray-800 p-5 bg-white rounded-lg shadow-lg">🤖 Retriving ...</div>
    {:else if data.length == 0}
        <div class="noresult text-gray-800 p-5 bg-white rounded-lg shadow-lg">❗️No result 😭😱🥺</div>
    {:else}
        <DataTable 
            on:changeFilter={handleChangeFilter}
            on:changePage={handleChangePage}
            on:changeSort={handleChangeSort}
            actions={datatableActions}
            attributes={datatableAttributes}   
            bind:data={data} 
            bind:activeFilter={filter}
            bind:page={page}
            bind:sortAttribute={sortAttribute}
            bind:sortDirection={sortDirection}
        />
    {/if}

</Container>



