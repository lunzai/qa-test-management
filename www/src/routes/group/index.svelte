<script context="module">
    import { loginIsValid } from '@app/accessControl';
    import * as testGroupApi from '@app/api/testGroup';
    import * as utils from '@app/utils';

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
            const response = await testGroupApi.list({ 'per-page': perPage, page, sort, ...filter }, token);
            if (response.success) {
                data = response.data;
            } else {
                this.error(400, '😰 Unable to load data.');
            }            
        } catch (e) {
            console.error('Group', e);
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
    import ListingTestCount from '../../components/ListingTestCount.svelte';
    import {
        STATUS_ACTIVE,
        STATUS_INACTIVE,
        STATUS_DEPLOYED,
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
        TEST_STATUS_UNABLE_TO_TEST,
    } from '@app/constants';
    
    // TODO: move everything to active data table?
    export let query;
    export let data;
    export let filter;
    export let page;
    export let sortAttribute;
    export let sortDirection;
    
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
    ];
    
    async function redirect() {
        let urlQuery = utils.urlQuery(query);
        await goto(`/group?${urlQuery}`);
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
            attribute: 'name',
            label: 'Name', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'Name',
        },
        { 
            attribute: 'status',
            label: 'Status', 
            sortable: true,
            filterable: true,
            filter: [
                { label: STATUS_ACTIVE, value: STATUS_ACTIVE },
                { label: STATUS_INACTIVE, value: STATUS_INACTIVE },
                { label: STATUS_DEPLOYED, value: STATUS_DEPLOYED },
            ],
        },
        { 
            attribute: 'test_status',
            label: 'Test Result', 
            sortable: true,
            filterable: true,
            component: Status,
            componentParams: model => {
                return { status: model.test_status, label: model.test_status }  
            },
            filter: [
                { label: TEST_STATUS_PASSED, value: TEST_STATUS_PASSED },
                { label: TEST_STATUS_FAILED, value: TEST_STATUS_FAILED },
                { label: TEST_STATUS_PENDING, value: TEST_STATUS_PENDING },
                { label: TEST_STATUS_UNABLE_TO_TEST, value: TEST_STATUS_UNABLE_TO_TEST },
            ],
        },
        {
            label: 'Test Count',
            sortable: false,
            filterable: false,
            component: ListingTestCount,
            componentParams: model => {
                return { passed: model.passed_count, failed: model.failed_count, pending: model.pending_count }  
            },
        }
    ];

    const datatableActions = [
        {
            label: null,
            icon: 'fa fa-folder-open',
            color: 'blue',
            href: row => `/group/${row.id}`,
        },
        {
            label: null,
            icon: 'fa fa-pen',
            color: 'teal',
            href: row => `/group/${row.id}/update`,
        },
    ];


</script>

<style>
    div.noresult { box-shadow: rgba(0,0,0,.05) 0 4px 20px 0 }
</style>

<svelte:head>
    <title>Test Groups</title>
</svelte:head>

<PageTitle title="Test Group" {breadcrumb}>
    <div class="">
        <Button 
            size="sm"
            label="Create"
            color="green"
            href="/group/create"
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



