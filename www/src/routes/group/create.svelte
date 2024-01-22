<script context="module">
    import { loginIsValid } from '@app/accessControl';    
    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
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
    import ResultProgress from '../../components/ResultProgress.svelte';
    import Model from '@app/models/TestGroup';
    import moment from 'moment';
    import Form from './_form.svelte';
    import { goto, stores } from '@sapper/app';

    const { session } = stores()
    const model = new Model({  
        token: $session.token
    });
    const title = `Create Test Group`;
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<PageTitle {title}>
    <div class="">
        <Button 
            size="sm"
            label="Cancel"
            color="gray"
            href="/group"
            isLink={true}
        />
    </div>
</PageTitle>

<Container>
    <Card title={title}>
        <Wrapper wrapperClass="sm:pt-0">

            <Form
                {model}
            />         

        </Wrapper>
    </Card>
</Container>