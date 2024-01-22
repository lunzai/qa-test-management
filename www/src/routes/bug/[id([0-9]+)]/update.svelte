<script context="module">
    import Model from '@app/models/BugFeature';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new Model({ token });
            const result = await model.find(params.id);
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

    const title = `Update Bug Report: ${model.get('title')}`;
    const breadcrumb = [
        { label: 'Home', href: '/' },
        { label: 'Bug Feature', href: '/bug' },
        { label: model.get('title'), href: `/bug/${model.getId()}` },
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
            href="/bug/{model.getId()}"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>
    <Card title={title}>
        <Wrapper wrapperClass="sm:pt-0">

            <Form
                {model}
                token={$session.token}
            />

        </Wrapper>
    </Card>
</Container>