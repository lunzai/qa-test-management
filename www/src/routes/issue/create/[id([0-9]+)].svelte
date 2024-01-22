<script context="module">
    import TestGroupModel from '@app/models/TestGroup';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            console.warn('Login is invalid');
            await this.fetch('/user/logout');
            return this.redirect(302, '/user/login');
        } 
        try {
            const group = new TestGroupModel({ token });
            const result = await group.find(params.id, {});
            if (result) {
                return { group };
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
    import moment from 'moment';
    import Form from '../_form.svelte';
    import TestIssueModel from '@app/models/TestIssue';
    import { stores } from '@sapper/app';

    export let group;
    const { session } = stores();
    const { token } = $session;
    const model = new TestIssueModel({
        model: { group_id: group.getId() },
        token,
    });
    const title = `Test Group: ${group.get('name')} - Add Issue`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Group', href: '/group' },
        { label: group.get('name'), href: `/group/${group.getId()}` },
        { label: 'Add Issue' }
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
            href="/group/{group.getId()}"
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
                group={group.attributes}
            />          

        </Wrapper>
    </Card>
</Container>