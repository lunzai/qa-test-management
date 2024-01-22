<script context="module">
    import Model from '@app/models/TestGroup';
    import * as Api from '@app/api/testGroup';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            let starredGroups;
            const model = new Model({ token });
            const result = await model.find(params.id, { expand: 'issues' });
            const findStarred = await Api.getStarred(token);
            if (findStarred.success) {
                starredGroups = findStarred.data;
            }
            if (!result) {
                return this.error(500, '🙀 Oh no. Something wrong?!');
            }
            return { model, starredGroups }
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
    import ListIssue from '../_listTestIssue.svelte';
    import * as alert from '../../../components/alert/alert.js';
    import Swal from 'sweetalert2';
    import moment from 'moment';
    import { FORMAT_DATETIME_LONG } from '@app/configs';
    import { goto, stores } from '@sapper/app'
    import {
        TEST_STATUS_PASSED,
        TEST_STATUS_FAILED,
        TEST_STATUS_PENDING,
    } from '@app/constants';

    export let starredGroups;
    export let model;
    let isStarred = starredGroups.some(group => group.id === model.getId());
    let starButtonLoading = false;

    const { session } = stores();
    const title = `Test Group: ${model.get('name')}`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
        { label: model.get('name'), href: `/group/${model.getId()}` }
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
                    Swal.fire('Deleted', '', 'success').then(goto('/group'));
                } catch (error) {
                    Swal.fire('Error', 'Unable to delete', 'error');
                }
            }
        });
    }

    async function handleStar() {
        starButtonLoading = true;
        try {
            const response = isStarred ? 
                await Api.unstar(model.getId(), $session.token) : 
                await Api.star(model.getId(), $session.token);
            if (response.success) {
                isStarred = !isStarred;
            }
            starButtonLoading = false;
        } catch (e) {
            alert.danger(`❗️Unable to add Group to favourite.`);
            console.error(e);
            starButtonLoading = false;
        }
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
            href="/group/{model.getId()}/update"
            isLink={true}
        />
        {#if isStarred}
            <Button 
                buttonClass="ml-1"
                size="sm"
                icon="fas fa-star"
                color="yellow"
                type="button"
                on:click={handleStar} 
                isLoading={starButtonLoading}
            />
        {:else}
            <Button 
                buttonClass="ml-1"
                size="sm"
                icon="far fa-star"
                color="yellow"
                type="button"
                on:click={handleStar}
                isLoading={starButtonLoading}
            />
        {/if}
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

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('name')}
                </div>
                <div slot="content">
                    {model.get('name')}
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('status')}
                </div>
                <div slot="content">                    
                    <Status status={model.get('status')} />
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('test_status')}
                </div>
                <div slot="content">
                    <Status status={model.get('test_status')} />
                </div>
            </DetailedRow>

            <DetailedRow>
                <div slot="label">
                    {model.getLabel('created_at')}
                </div>
                <div slot="content">
                    {moment.unix(model.get('created_at')).format(FORMAT_DATETIME_LONG)}
                </div>
            </DetailedRow>            

            <hr class="mb-6 mt-3" />

            <div class="flex text-sm text-gray-500">
                <Status count="{model.get('passed_count')}" status={TEST_STATUS_PASSED} />
                <Status count="{model.get('failed_count')}" status={TEST_STATUS_FAILED} marginLeft="2" />
                <Status count="{model.get('pending_count')}" status={TEST_STATUS_PENDING} marginLeft="2" />
            </div>
            <ResultProgress 
                items={[
                    { label: 'Passed', count: model.get('passed_count'), color: 'green' },                                
                    { label: 'Failed', count: model.get('failed_count'), color: 'red' },
                    { label: 'Pending', count: model.get('pending_count'), color: 'gray' },
                ]}
            />

        </Wrapper>
    </Card>
</Container>

<Container>
    <div class="flex items-center justify-start mt-12">
        <h3 class="flex-grow text-2xl leading-tight font-medium text-gray-900">Test Issues</h3>
        <div>
            <Button 
                size="sm"
                label="Add Test Issue"
                color="green"
                href="/issue/create/{model.getId()}"
                isLink={true}
            />
        </div>
    </div>
</Container>

<ListIssue
    models={model.getIssues()}
/>
