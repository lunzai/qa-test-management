<script context="module">
    import { loginIsValid } from '@app/accessControl';
    import * as userApi from '@app/api/user';
    import * as utils from '@app/utils';

    export async function preload({ params, query }, { user, token }) {
        if (!user.is_admin) {
            return this.error(400, '🤬 Unauthorized access, you are not supposed to be here!')
        }
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
            const response = await userApi.list({ 'per-page': perPage, page, sort, ...filter }, token);
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
    import Container from '../../../components/Container.svelte';
    import Card from '../../../components/Card.svelte';
    import Wrapper from '../../../components/Wrapper.svelte';
    import DataTable from '../../../components/DataTable.svelte';
    import PageTitle from '../../../components/PageTitle.svelte';
    import Button from '../../../components/Button.svelte';

    import {
        STATUS_ACTIVE,
        STATUS_DELETED,
        JOB_ROLE_USER,
        JOB_ROLE_DEVELOPER,
        JOB_ROLE_QA,
        JOB_ROLE_PM,
        JOB_ROLE_MANAGEMENT,
    } from '@app/constants';
    import { goto } from '@sapper/app';

    export let query;
    export let data;
    export let filter;
    export let page;
    export let sortAttribute;
    export let sortDirection;

    async function redirect() {
        let urlQuery = utils.urlQuery(query);
        await goto(`/admin/user?${urlQuery}`);
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
            attribute: 'email',
            label: 'Email', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'Email',
        },
        { 
            attribute: 'display_name',
            label: 'Name', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'Name',
        },
        { 
            attribute: 'job_role',
            label: 'Job Role', 
            sortable: true,
            filterable: true,
            filter: [
                { label: JOB_ROLE_USER, value: JOB_ROLE_USER },
                { label: JOB_ROLE_DEVELOPER, value: JOB_ROLE_DEVELOPER },
                { label: JOB_ROLE_QA, value: JOB_ROLE_QA },
                { label: JOB_ROLE_PM, value: JOB_ROLE_PM },
                { label: JOB_ROLE_MANAGEMENT, value: JOB_ROLE_MANAGEMENT },
            ],
        },
        { 
            attribute: 'status',
            label: 'Status', 
            sortable: true,
            filterable: true,
            filter: [
                { label: STATUS_ACTIVE, value: STATUS_ACTIVE },
                { label: STATUS_DELETED, value: STATUS_DELETED },
            ],
        },
        { 
            label: 'Role',
            sortable: false,
            filterable: false,
            value: model => model.roles,
        },
        { 
            attribute: 'lark_id',
            label: 'Lark ID', 
            sortable: true,
            filterable: true,
        }
    ];

    const datatableActions = [
        {
            label: null,
            icon: 'fa fa-folder-open',
            color: 'blue',
            href: row => `/admin/user/${row.id}`,
        },
    ];
</script>

<svelte:head>
	<title>Admin: User</title>
</svelte:head>

<PageTitle title="Admin: User">
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