<script context="module">
    import Model from '@app/models/bugFeature';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new Model({ token });
            const result = await model.find(params.id, { expand: 'reporter,qa,developer' });
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
    import Status from '../../../components/Status.svelte';
    import * as Api from '@app/api/bugFeature';
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
    const reporter = model.get('reporter');
    const developer = model.get('developer');
    const qa = model.get('qa');
    const title = `Bug Report: ${model.get('title')}`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Bug Report', href: '/bug' },
        { label: model.get('title'), href: `/bug/${model.getId()}` },
    ];

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
                    Swal.fire('Deleted', '', 'success').then(goto(`/bug`));
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
            href="/bug/{model.getId()}/update"
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
    <Card title={model.get('title')}>
        <Wrapper wrapperClass="sm:pt-0">

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('title')}
                        </div>
                        <div slot="content">
                            {model.get('title')}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('status')}
                        </div>
                        <div slot="content">
                            <Status status={model.get('status')} />
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('reporter_user_id')}
                        </div>
                        <div slot="content">
                            {reporter ? reporter.display_name : '-'}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('priority')}
                        </div>
                        <div slot="content">
                            <Status status={model.get('priority')} />
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
                            {model.getLabel('fix_status')}
                        </div>
                        <div slot="content">
                            <Status status={model.get('fix_status')} />
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('developer_user_id')}
                        </div>
                        <div slot="content">
                            {developer ? developer.display_name : '-'}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('jira_number')}
                        </div>
                        <div slot="content">
                            <Status status={model.get('jira_number')} />
                        </div>
                    </DetailedRow>
                </div>
            </div>

            <div class="flex flex-wrap">
                <div class="w-full sm:w-1/2 sm:pr-3">
                    <DetailedRow smCols={2} smLeftCol={1} mdCols={5} mdLeftCol={2}>
                        <div slot="label">
                            {model.getLabel('created_at')}
                        </div>
                        <div slot="content">
                            {moment.unix(model.get('created_at')).format(FORMAT_DATETIME_LONG)}
                        </div>
                    </DetailedRow>
                </div>
                <div class="w-full sm:w-1/2 sm:pl-3">
                    
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

        </Wrapper>
    </Card>
</Container>

<!-- <Container>
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
/> -->
