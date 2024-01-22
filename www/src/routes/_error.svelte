<script>
	import Container from '../components/Container.svelte';
    import Wrapper from '../components/Wrapper.svelte';
    import PageTitle from '../components/PageTitle.svelte';
    import Card from '../components/Card.svelte';

	export let status;
	export let error;
</script>

<svelte:head>
	<title>{status}</title>
</svelte:head>

<PageTitle title="Error"></PageTitle>

<Container>
    <Card>
	    <Wrapper>
			<div class="text-center pb-6">
				<img class="h-48 w-48 mx-auto mt-10" src="/images/error.svg" alt="OH NO!" />

				<h1 class="font-mono text-2xl mt-6 tracking-widest">{status}</h1>
				<p class="text-lg tracking-wide">{error.message}</p>			
			</div>

			{#if error.stack}
				<pre class="p-6 bg-gray-100 whitespace-pre-wrap m-6 overflow-auto text-sm shadow">{error.stack}</pre>
			{/if}
	    </Wrapper>	
    </Card>
</Container>

