<script>
    import Container from '../../components/Container.svelte';
    import Wrapper from '../../components/Wrapper.svelte';
    import PageTitle from '../../components/PageTitle.svelte';
    import Button from '../../components/Button.svelte';
    import Card from '../../components/Card.svelte';
    let isLoading;
    let email;
    let validate = {
        email: false
    };
    let canSubmit;

    async function handleSubmit() {

    }
</script>


<svelte:head>
    <title>Forget Password</title>
</svelte:head>

<PageTitle title="Forget Password"></PageTitle>

<Container>
    <Wrapper>
        <Card>
            <div class="px-6 py-40 text-center text-2xl">
                🚨🚧 Under construction 🚧🚨
            </div>
        </Card>
    </Wrapper>
</Container>
