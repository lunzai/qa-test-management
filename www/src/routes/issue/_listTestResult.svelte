<script>
    import Collapse from '../../components/Collapse.svelte';
    import Card from '../../components/Card.svelte';
    import Status from '../../components/Status.svelte';
    import DetailedRow from '../../components/DetailedRow.svelte';
    import Button from '../../components/Button.svelte';
    import TestResultModel from '@app/models/TestResult';
    import moment from 'moment';
    import { FORMAT_DATETIME_LONG } from '@app/configs';

    export let testCase;
    export let active;
    export let results = [];

    const now = Math.floor(Date.now() / 1000);
    const model = new TestResultModel({});
    
</script>

<div class="my-6 flex items-center justify-start">
    <h5 class="font-semibold text-md flex-grow">Test Result(s)</h5>
    <Button
        label="Submit Test Result"
        isLink={true}
        size="sm"
        color="green"
        buttonClass="mt-3"
        href="/test-result/create/{testCase.id}"
    />
</div>

<div class="result-wrapper">
    {#if results.length}
    <div>
        {#each results as result, i (result.id)}
            <!-- TODO: Add to constant file -->
            <Collapse 
                id="result-{result.id}"
                isOpen={active == result.id}
                active={result.status == 'Active'}
            >
                <div slot="header" class="flex items-end w-full">
                    <strong>#{result.id}</strong>
                    <Status marginLeft={3} status={result.test_status} />
                    <span class="text-xs text-gray-500 ml-3 flex-grow">
                        Submitted 
                            {
                                now - parseInt(result.created_at) < 86400 ? 
                                    moment.unix(result.created_at).fromNow() :
                                    ` at ` + moment.unix(result.created_at).format(FORMAT_DATETIME_LONG)
                            } 
                        by {result.user.display_name}
                    </span>
                    <!-- TODO: Add to constant file -->
                    {#if result.status == 'Inactive'}
                        <Status status={result.status} />
                    {/if}
                </div>
                <div slot="body" class="pb-1">

                    <div class="flex flex-wrap">
                        <div class="w-full sm:w-1/2 sm:pr-3">
                            <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                <div slot="label">
                                    {model.getLabel('tester_user_id')}
                                </div>
                                <div slot="content">
                                    {result.tester_user_id}
                                </div>
                            </DetailedRow>
                        </div>
                        <div class="w-full sm:w-1/2 sm:pl-3">
                            <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                <div slot="label">
                                    {model.getLabel('created_at')}
                                </div>
                                <div slot="content">
                                    {moment.unix(result.created_at).format(FORMAT_DATETIME_LONG)}
                                </div>
                            </DetailedRow>
                        </div>
                    </div>

                    <div class="flex flex-wrap">
                        <div class="w-full sm:w-1/2 sm:pr-3">
                            <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                <div slot="label">
                                    {model.getLabel('version')}
                                </div>
                                <div slot="content">
                                    {result.version ? result.version : '-'}
                                </div>
                            </DetailedRow>
                        </div>
                        <div class="w-full sm:w-1/2 sm:pl-3">
                            <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                <div slot="label">
                                    {model.getLabel('platform')}
                                </div>
                                <div slot="content">
                                    {result.platform ? result.platform : '-'}
                                </div>
                            </DetailedRow>
                        </div>
                    </div>

                    <div class="flex flex-wrap mb-3">
                        <div class="w-full sm:w-1/2 sm:pr-3">
                            <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2} lgCols={5} lgLeftCol={2}>
                                <div slot="label">
                                    {model.getLabel('test_status')}
                                </div>
                                <div slot="content">
                                    <Status status={result.test_status} />
                                </div>
                            </DetailedRow>
                        </div>
                        <div class="w-full sm:w-1/2 sm:pl-3">
                        </div>
                    </div>

                    <hr />

                    <div class="font-semibold mt-3 mb-1">
                        {model.getLabel('actual_result')}
                    </div>
                    <div class="html-content">
                        {@html result.actual_result}
                    </div>

                </div>
            </Collapse>
        {/each}
    </div>
    {:else}
        <div class="noresult text-gray-800">❗️No result 😭😱🥺</div>
    {/if}
    
</div>
