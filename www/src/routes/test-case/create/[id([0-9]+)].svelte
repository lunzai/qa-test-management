<script context="module">
    import TestIssueModel from '@app/models/TestIssue';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            let issue = new TestIssueModel({ token });
            let result = await issue.find(params.id, { expand: 'group' });
            if (result) {
                return { issue };
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
    import TestCaseModel from '@app/models/TestCase';
    import moment from 'moment';
    import Form from '../_form.svelte';
    import { stores } from '@sapper/app';

    export let issue;
    const group = issue.get('group');
    
    const { session } = stores();
    const { token } = $session;
    const model = new TestCaseModel({
        model: { issue_id: issue.getId() },
        token,
    });
    const title = `Test Issue: ${issue.get('name')} - Add Test Case`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
        { label: group.name, href: `/group/${group.id}` },
        { label: issue.get('name'), href: `/issue/${issue.getId()}` },
        { label: 'Add Test Case' }
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
            href="/issue/{issue.getId()}"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>
    <Card title="Add Issue">
        <Wrapper wrapperClass="sm:pt-0">

            <Form
                {model}
                {token}
                issue={issue.attributes}
            />

        </Wrapper>
    </Card>
</Container>