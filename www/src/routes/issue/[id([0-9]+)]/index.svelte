<script context="module">
    import Model from '@app/models/TestIssue';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new Model({ token });
            const result = await model.find(params.id, { expand: 'group,testCases,testCases.results,testCases.results.user,qa,developer' });
            if (result) {
                return { model };
            } else {
                return this.error(500, '🙀 Oh no. Something wrong?!');
            }
        } catch (error) {
            this.error(500, error.name + ': ' + error.message);
        }
    }
</script>

<script>
    import Container from '../../../components/Container.svelte';
    import Wrapper from '../../../components/Wrapper.svelte';
    import PageTitle from '../../../components/PageTitle.svelte';
    import Card from '../../../components/Card.svelte';
    import Button from '../../../components/Button.svelte';
    import DetailedRow from '../../../components/DetailedRow.svelte';
    import ResultProgress from '../../../components/ResultProgress.svelte';
    import Status from '../../../components/Status.svelte';
    import ListTestCase from '../_listTestCase.svelte';
    import * as Api from '@app/api/testIssue';
    import Swal from 'sweetalert2';
    import moment from 'moment';
    import { FORMAT_DATETIME_LONG } from '@app/configs';
    import { stores, goto } from '@sapper/app';
    import {
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
    } from '@app/constants';

    export let model;
    const { session, page } = stores();
    const group = model.get('group');
    const developer = model.get('developer');
    const qa = model.get('qa');
    const title = `Test Issue: ${model.get('name')}`;
    const issueId = model.getId();
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
        { label: group.name, href: `/group/${group.id}` },
        { label: model.get('name'), href: `/issue/${model.getId()}` },
    ];
    let testCases = model.getTestCases();

    async function handleDelete() {
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
                    const response = await Api.remove(model.getId(), $session.token);
                    Swal.fire('Deleted', '', 'success').then(goto(`/group/${model.get('group_id')}`));
                } catch (error) {
                    Swal.fire('Error', 'Unable to delete', 'error');
                }
            }
        });
    }
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<PageTitle {title} {breadcrumb}>
    <div class="flex flex-no-wrap">
        <Button 
            size="sm"
            label="Update"
            href="/issue/{model.getId()}/update"
            isLink={true}
        />
        <Button 
            buttonClass="ml-1"
            size="sm"
            icon="fas fa-trash"
            color="red"
            type="button"
            on:click={handleDelete}
        />
    </div>
</PageTitle>

<Container>
    <Card title={model.get('name')}>
        <Wrapper wrapperClass="sm:pt-0">

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('name')}
                        </div>
                        <div slot="content">
                            {model.get('name')}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('group_id')}
                        </div>
                        <div slot="content">
                            {group.name}
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('status')}
                        </div>
                        <div slot="content">
                            <Status status={model.get('status')} />
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('created_at')}
                        </div>
                        <div slot="content">
                            {moment.unix(model.get('created_at')).format(FORMAT_DATETIME_LONG)}
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('qa_user_id')}
                        </div>
                        <div slot="content">
                            {qa ? qa.display_name : '-'}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('developer_user_id')}
                        </div>
                        <div slot="content">
                            {developer ? developer.display_name : '-'}
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('jira_number')}
                        </div>
                        <div slot="content">
                            {model.get('jira_number') ? model.get('jira_number') : '-'}
                            {#if model.get('jira_url')}
                                <a
                                    target="_blank"
                                    href={model.get('jira_url')}
                                    alt={model.get('jira_number')}
                                    class="text-blue-400 hover:text-blue-600 text-xs ml-2 transition-all duration-300 cursor-pointer"
                                ><i class="fa fa-external-link"></i> See in JIRA</a>
                            {/if}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('lark_url')}
                        </div>
                        <div slot="content">
                            {#if model.get('lark_url')}
                                <a
                                    target="_blank"
                                    href={model.get('lark_url')}
                                    alt={model.get('Lark Spec')}
                                    class="text-blue-400 hover:text-blue-600 text-xs ml-2 transition-all duration-300 cursor-pointer"
                                ><i class="fa fa-external-link"></i> See in LARK</a>
                            {:else}
                                -
                            {/if}
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('test_status')}
                        </div>
                        <div slot="content">
                            <Status status={model.get('test_status')} />
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            # of Test Case
                        </div>
                        <div slot="content">
                            {model.get('testCases').length}
                        </div>
                    </DetailedRow>
                </div>
            </div>

            {#if model.get('description')}
                <hr class="mb-6 mt-3" />

                <div>
                    <h5 class="font-semibold text-md mb-3">{model.getLabel('description')}</h5>
                    <div class="html-content">
                        {@html model.get('description') ? model.get('description') : ''}
                    </div>
                </div>
            {/if}

            <hr class="mb-6 mt-3" />

            <div class="flex text-sm text-gray-500">
                <Status count="{model.get('passed_count')}" status={TEST_STATUS_PASSED} />
                <Status count="{model.get('failed_count')}" status={TEST_STATUS_FAILED} marginLeft="2" />
                <Status count="{model.get('pending_count')}" status={TEST_STATUS_PENDING} marginLeft="2" />
            </div>

            <ResultProgress 
                items={[
                    { label: TEST_STATUS_PASSED, count: model.get('passed_count'), color: 'green' },                                
                    { label: TEST_STATUS_FAILED, count: model.get('failed_count'), color: 'red' },
                    { label: TEST_STATUS_PENDING, count: model.get('pending_count'), color: 'gray' },
                ]}
            />

        </Wrapper>
    </Card>
</Container>

<Container>
    <div class="flex items-center justify-start mt-12">
        <h3 class="flex-grow text-2xl leading-tight font-medium text-gray-900">Test Cases</h3>
        <div>
            <Button 
                size="sm"
                label="Add Test Case"
                color="green"
                href="/test-case/create/{model.getId()}"
                isLink={true}
            />
        </div>
    </div>
</Container>

<ListTestCase 
    models={testCases}
    {issueId}
/>
