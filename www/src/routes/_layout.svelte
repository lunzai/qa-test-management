<script>
	import Navbar from '../components/Navbar.svelte';
	import Alerts from '../components/alert/Alerts.svelte';
    import { stores } from '@sapper/app';
    
    const { page, session } = stores();
    let user;
    $: user = $session.user;
</script>

<Alerts />

{#if user}
	<Navbar {user} />
{/if}
<slot></slot>
