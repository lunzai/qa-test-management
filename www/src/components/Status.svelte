<script>
    export let color;
    export let marginLeft;
    export let count;
    export let status;
    export let label;
    import {
        STATUS_ACTIVE,
        STATUS_INACTIVE,
        STATUS_DEPLOYED,
        
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
        TEST_STATUS_UNABLE_TO_TEST,

        PRIORITY_LOW, PRIORITY_MEDIUM, PRIORITY_HIGH, PRIORITY_BLOCKER,
        
        FIX_STATUS_PENDING_INVESTIGATION, FIX_STATUS_UNABLE_TO_REPLICATE,
        FIX_STATUS_NEED_MORE_INFORMATION, FIX_STATUS_WORK_IN_PROGRESS,
        FIX_STATUS_TO_DO, FIX_STATUS_FIXED, FIX_STATUS_KIV
    } from '@app/constants';

    if (!color) {
        switch (status) {
            case TEST_STATUS_PASSED:
            case STATUS_ACTIVE:
            case STATUS_DEPLOYED:
            case FIX_STATUS_FIXED:
                color = 'green';
                break;
            case TEST_STATUS_FAILED:
            case STATUS_INACTIVE:
            case PRIORITY_BLOCKER:
                color = 'red';
                break;
            case TEST_STATUS_UNABLE_TO_TEST:
            case PRIORITY_HIGH: 
                color = 'orange';
                break;
            case PRIORITY_MEDIUM: 
            case FIX_STATUS_PENDING_INVESTIGATION: 
                color = 'yellow';
                break;
            case TEST_STATUS_PENDING:
            case PRIORITY_LOW: 
            case FIX_STATUS_UNABLE_TO_REPLICATE:          
            case FIX_STATUS_KIV:            
                color = 'gray';
                break;
            case FIX_STATUS_WORK_IN_PROGRESS:
                color = 'blue';
                break;
            case FIX_STATUS_TO_DO:
            case FIX_STATUS_NEED_MORE_INFORMATION:
                color = 'teal';
                break;
        }
    }

    if (!label) {
        label = count == null ? status : `${count} ${status}`;
    }
</script>

<span data-cy="status" class="flex items-center {marginLeft ? `ml-${marginLeft}` : ''}">
    <span class="inline-block h-2 w-2 rounded-full mr-2 bg-{color}-500"></span>
    <span class="text-{color}-500">{label}</span>
</span>