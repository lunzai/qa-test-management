<script context="module">
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            console.warn('Login is invalid');
            await this.fetch('/user/logout');
            return this.redirect(302, '/user/login');
        }
    }
</script>

<script>
    import Container from '../../components/Container.svelte';
    import Wrapper from '../../components/Wrapper.svelte';
    import PageTitle from '../../components/PageTitle.svelte';
    import Card from '../../components/Card.svelte';
    import Button from '../../components/Button.svelte';
    import DetailedRow from '../../components/DetailedRow.svelte';
    import moment from 'moment';
    import Form from './_form.svelte';
    import BugFeatureModel from '@app/models/BugFeature';
    import { stores } from '@sapper/app';
    import {  TYPE_BUG } from '@app/constants';

    const { session } = stores();
    const { token, user } = $session;
    const model = new BugFeatureModel({
        model: { type: TYPE_BUG, reporter_user_id: user.id },
        token,
    });
    const title = `Add Bug Report`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Bug Report', href: '/bug' },
        { label: 'Add Bug' }
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
            href="/bug"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>
    <Card title="Add Bug Report">
        <Wrapper wrapperClass="sm:pt-0">

            <Form                
                {model}
                {token}
            />          

        </Wrapper>
    </Card>
</Container>