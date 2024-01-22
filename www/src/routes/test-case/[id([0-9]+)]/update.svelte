<script context="module">
    import TestCaseModel from '@app/models/TestCase';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new TestCaseModel({ token });
            const result = await model.find(params.id, { expand: 'issue,issue.group' });
            if (result) {
                return { model };
            } else {
                return this.error(500, '🙀 Oh no. Something wrong?!');
            }
        } catch (error) {
            return this.error(500, error.name + ': ' + error.message);
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
    import moment from 'moment';
    import Form from '../_form.svelte';
    import { goto, stores } from '@sapper/app';

    export let model;
    const { session } = stores();

    const issue = model.get('issue');
    const group = issue.group;
    const title = `Update Test Case: ${model.get('short_description')}`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
        { label: group.name, href: `/group/${group.id}` },
        { label: issue.name, href: `/issue/${issue.id}` },
        { label: model.get('short_description'), href: `/issue/${issue.id}?tab=${model.getId()}` },
        { label: 'Update' }
    ];
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<PageTitle {title} {breadcrumb}>
    <div class="">
        <Button 
            size="sm"
            label="Cancel"
            color="gray"
            href="/issue/{issue.id}?tab={model.getId()}"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>
    <Card title={title}>
        <Wrapper wrapperClass="sm:pt-0">

            <Form
                {issue}
                {group}
                {model}
                token={$session.token}
            />         

        </Wrapper>
    </Card>
</Container>