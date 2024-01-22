<script>
    import TestCaseModel from '@app/models/TestCase';
    import Container from '../../components/Container.svelte';
    import Wrapper from '../../components/Wrapper.svelte';
    import DetailedRow from '../../components/DetailedRow.svelte';
    import ResultProgress from '../../components/ResultProgress.svelte';
    import Status from '../../components/Status.svelte';
    import Button from '../../components/Button.svelte';
    import ListTestResult from './_listTestResult.svelte';
    import * as scroll from "svelte-scrollto";
    import moment from 'moment';
    import * as Api from '@app/api/testCase';
    import Swal from 'sweetalert2';
    import { debounce } from '@app/utils';
    import { FORMAT_DATETIME_LONG } from '@app/configs';
    import { stores, goto } from '@sapper/app';
    import { onMount, createEventDispatcher } from 'svelte';
    import {
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
    } from '@app/constants';

    export let tabPrefix = 'tab-';
    export let activeTab;
    export let models;
    export let issueId;

    const { session, page } = stores();
    const { query } = $page;
    const testCaseModel = new TestCaseModel({});

    let searchTestCase = '';
    let activeResult;
    let filteredModels = models;

    async function handleDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F56565',
            cancelButtonColor: '#A0AEC0',
            confirmButtonText: 'Yes, delete it!'
        })
        .then(async result => {
            if (result.value) {
                try {
                    const response = await Api.remove(id, $session.token);
                    Swal.fire('Deleted', '', 'success').then(() => {
                        location.href = `/issue/${issueId}`;
                    });
                } catch (error) {
                    Swal.fire('Error', 'Unable to delete', 'error');
                }
            }
        });
    }

    function search() {
        filteredModels = models.filter(model => {
            const search = searchTestCase.trim();
            if (searchTestCase == null || search == '') {
                return true;
            } else {
                const searchArray = search.split(",");
                return searchArray.some(searchTerm => {
                    if (searchTerm == '') {
                        return false;
                    }
                    const regex = new RegExp(`${searchTerm}`, 'ig');
                    return (model.id == searchTerm || regex.exec(model.short_description) !== null);
                });
            }
        });
        if (filteredModels.length) {
            handleSelectTab(filteredModels[0].id);
        } 
    }

    $: {
        if (!activeTab && models[0]) {
            activeTab = `${tabPrefix}${models[0].id}`;
        }
    }

    function handleSelectTab(id) {
        activeTab = `${tabPrefix}${id}`;
    }

    onMount(() => {
        if (query.tab && /^[0-9]+$/.test(query.tab)) {
            activeTab = `tab-${query.tab}`;
            if (query.result && /^[0-9]+$/.test(query.result)) {
                activeResult = parseInt(query.result, 10);
                // scroll.scrollTo({ element: `#result-${activeResult}`, duration: 500, offset: -20 }); not working
                scroll.scrollTo({ element: '#tab', duration: 500, offset: -20 });
            } else {
                scroll.scrollTo({ element: '#tab', duration: 500, offset: -20 });
            }
        }

	});
</script>

<style>
    .search-box, button.active::after {
        content: "";
        width: 100%;
        bottom: 0;
        left: 0;
    }
    div.active {
        display: block;
    }
    .search-box, button.active {
        z-index: 5;
        background: #fff;
    }
</style>

<Container>
    
    {#if filteredModels.length}
        <div id="tab" class="flex flex-wrap rounded-lg overflow-hidden shadow-lg">
            <div class="w-1/5 bg-gray-100">
                <div class="px-5 pt-4 pb-5 w-full block bg-white relative search-box border-b border-gray-200">
                    <input 
                        on:keydown={debounce(search, 500)}
                        bind:value={searchTestCase} 
                        type="text" 
                        class="py-2 px-4 appearance-non border rounded-md block w-full  
                        border-gray-300 text-base text-gray-700 focus:outline-none"
                        placeholder="Search"
                    />
                </div>
                {#each filteredModels as model, i (model.id)}
                    <button 
                        on:click={() => handleSelectTab(model.id)}
                        class:active={activeTab == `${tabPrefix}${model.id}`}
                        type="button" 
                        id="tab-{model.id}" 
                        class="tab-button p-5 mb-px relative w-full block focus:outline-none 
                        text-left hover:text-indigo-500 border-b border-gray-200 last:border-none
                        transition-all duration-200"
                    >
                        <p class="text-xs text-gray-800 mb-1">ID #{model.id}</p>
                        <p class="font-semibold">
                            {model.short_description}                            
                        </p>
                        <!-- TODO: Add to constant file -->
                        <p class="text-xs mt-1">
                            {#if model.status == 'Active'}
                                <Status label="{model.test_status} - {model.results.length} test result(s)" count={model.passed_count} status={model.test_status} />
                            {:else}
                                <Status status={model.status} />
                            {/if}
                        </p>
                    </button>
                {/each}
            </div>
            <div class="w-4/5 shadow-lg bg-white">
                <Wrapper>
                    {#each models as model, i (model.id)}
                        <div 
                            id="{tabPrefix}{model.id}"
                            class:active={activeTab == `${tabPrefix}${model.id}`}
                            class="hidden transition-all duration-200"
                        >
                            <div class="mb-3">
                                <div class="flex flex-wrap">
                                    <div class="w-full sm:w-1/2 sm:pr-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                {testCaseModel.getLabel('id')}
                                            </div>
                                            <div slot="content">
                                                {model.id}
                                            </div>
                                        </DetailedRow>
                                    </div>
                                    <div class="w-full sm:w-1/2 sm:pl-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                {testCaseModel.getLabel('status')}
                                            </div>
                                            <div slot="content">
                                                <Status status={model.status} />
                                            </div>
                                        </DetailedRow>
                                    </div>
                                </div>

                                <div class="flex flex-wrap">
                                    <div class="w-full sm:w-1/2 sm:pr-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                {testCaseModel.getLabel('platform')}
                                            </div>
                                            <div slot="content">
                                                {model.platform ? model.platform : '-'}
                                            </div>
                                        </DetailedRow>
                                    </div>
                                    <div class="w-full sm:w-1/2 sm:pl-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                {testCaseModel.getLabel('pre_condition')}
                                            </div>
                                            <div slot="content">
                                                {model.pre_condition ? model.pre_condition : '-'}
                                            </div>
                                        </DetailedRow>
                                    </div>
                                </div>

                                <div class="flex flex-wrap">
                                    <div class="w-full sm:w-1/2 sm:pr-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                {testCaseModel.getLabel('test_status')}
                                            </div>
                                            <div slot="content">
                                                <Status status={model.test_status} />
                                            </div>
                                        </DetailedRow>
                                    </div>
                                    <div class="w-full sm:w-1/2 sm:pl-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                {testCaseModel.getLabel('created_at')}
                                            </div>
                                            <div slot="content">
                                                {moment.unix(model.created_at).format(FORMAT_DATETIME_LONG)}
                                            </div>
                                        </DetailedRow>
                                    </div>
                                </div>

                                <div class="flex flex-wrap">
                                    <div class="w-full sm:w-1/2 sm:pr-3">
                                        <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                            <div slot="label">
                                                # of Test Result(s)
                                            </div>
                                            <div slot="content">
                                                {model.results.length}
                                            </div>
                                        </DetailedRow>
                                    </div>
                                    <div class="w-full sm:w-1/2 sm:pl-3">                                    
                                    </div>
                                </div>
                            </div>

                            <Button 
                                buttonClass="mb-6"
                                label="Update"
                                size="sm"
                                isLink={true}
                                href="/test-case/{model.id}/update" 
                            />

                            <Button 
                                size="sm"
                                icon="fas fa-trash"
                                color="red"
                                type="button"
                                on:click={() => handleDelete(model.id)}
                            />
                        
                            <hr />

                            {#if model.total_count > 0}
                                <div class="my-6">
                                    <div class="flex text-sm text-gray-500">
                                        <Status count="{model.passed_count}" status={TEST_STATUS_PASSED} />
                                        <Status count="{model.failed_count}" status={TEST_STATUS_FAILED} marginLeft="2" />
                                        <Status count="{model.pending_count}" status={TEST_STATUS_PENDING} marginLeft="2" />
                                    </div>

                                    <ResultProgress 
                                        items={[
                                            { label: TEST_STATUS_PASSED, count: model.passed_count, color: 'green' },                                
                                            { label: TEST_STATUS_FAILED, count: model.failed_count, color: 'red' },
                                            { label: TEST_STATUS_PENDING, count: model.pending_count, color: 'gray' },
                                        ]}
                                    />                            
                                </div>

                                <hr />
                            {/if}

                            <div class="my-6">
                                <h5 class="font-semibold text-md mb-3">{testCaseModel.getLabel('replicate_step')}</h5>
                                <div class="html-content">
                                    {@html model.replicate_step}
                                </div>
                            </div>

                            <hr />

                            <div class="my-6">
                                <h5 class="font-semibold text-md mb-3">{testCaseModel.getLabel('expected_result')}</h5>
                                <div class="html-content">
                                    {@html model.expected_result}
                                </div>
                            </div>

                            <hr />

                            <div class="my-6">
                                <h5 class="font-semibold text-md mb-3">{testCaseModel.getLabel('description')}</h5>
                                <div class="html-content">
                                    {@html model.description}
                                </div>
                            </div>

                            <hr />

                            <ListTestResult 
                                active={activeResult}
                                testCase={model}
                                results={model.results}
                            />

                        </div>
                    {/each}
                </Wrapper>
            </div>
        </div>
    {:else}
        <div class="noresult text-gray-800 p-5 bg-white rounded-lg shadow-lg">❗️No result 😭😱🥺</div>
    {/if}
</Container>