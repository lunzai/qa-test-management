<script>
    import Container from '../../components/Container.svelte';
    import Table from '../../components/Table.svelte';
    import Status from '../../components/Status.svelte';
    import ListingTestCount from '../../components/ListingTestCount.svelte';
    import {
        STATUS_ACTIVE,
        STATUS_INACTIVE,
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
        TEST_STATUS_UNABLE_TO_TEST,
    } from '@app/constants';

    export let models;

    const tableAttributes = [
        { 
            attribute: 'id',
            label: 'ID', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'ID',
        },
        { 
            attribute: 'jira_number',
            label: 'JIRA', 
            sortable: true,
            filterable: true,
            filterPlaceholder: 'JIRA',
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

    const tableActions = [
        {
            label: null,
            icon: 'fa fa-folder-open',
            color: 'blue',
            href: row => `issue/${row.id}`,
        },
        {
            label: null,
            icon: 'fa fa-pen',
            color: 'teal',
            href: row => `issue/${row.id}/update`,
        },
    ];
</script>

<Container>
    <Table
        attributes={tableAttributes}
        actions={tableActions}
        data={models}
        enableInactiveRow={true}
        isInactiveRow={row => row.status == STATUS_INACTIVE}
    >
    </Table>
</Container>
