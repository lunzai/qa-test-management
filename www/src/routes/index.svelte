<script context="module">
    import { loginIsValid } from '@app/accessControl';
    import * as testGroupApi from '@app/api/testGroup';
    let data;
    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const response = await testGroupApi.getStarred(token);
            if (response.success) {
                data = response.data;
            } else {
                this.error(400, '😰 Unable to load data.');
            }
        } catch (e) {
            console.error('Group', e);
            alert.danger(e.message);
        }
        return { data };
    }
</script>

<script>
	import Container from '../components/Container.svelte';
    import PageTitle from '../components/PageTitle.svelte';
    import Table from '../components/Table.svelte';
    import Status from '../components/Status.svelte';
    import ListingTestCount from '../components/ListingTestCount.svelte';
    import {
        STATUS_INACTIVE,
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
        TEST_STATUS_UNABLE_TO_TEST,
    } from '@app/constants';

    export let data;
    let attributes = [
        { 
            attribute: 'id',
            label: 'ID', 
        },
        { 
            attribute: 'name',
            label: 'Name', 
        },
        { 
            attribute: 'status',
            label: 'Status', 
        },
        { 
            attribute: 'test_status',
            label: 'Test Result', 
            component: Status,
            componentParams: model => {
                return { status: model.test_status, label: model.test_status }  
            },
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
    const actions = [
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

<svelte:head>
	<title>Dashboard</title>
</svelte:head>

<PageTitle
	title="Starred Groups"
>
</PageTitle>

<Container>
    <Table
        {attributes}
        {actions}
        {data}
        enableInactiveRow={true}
        isInactiveRow={row => row.status == STATUS_INACTIVE}
    >
    </Table>
</Container>