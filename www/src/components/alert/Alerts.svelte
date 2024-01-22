<script>
	import { slide } from 'svelte/transition';
	import { quintOut } from 'svelte/easing';
	import { onDestroy } from 'svelte';
	import { alertStore } from './store.js';	
	import AlertBlock from './Alert.svelte';
	
	let alerts = [];
	let unsubscribe;

	function addAlert(message, type, timeout) {
		let date = new Date();
		let id = date.getTime() + '.' + date.getMilliseconds() + '.' + alerts.length;
		alerts = [
			{ 
				id,
				message,
				type,
				timeout
			},
			...alerts
		];
		if (timeout) {
			setTimeout(removeAlert, timeout, id);	
		}
	}
	
	function removeAlert(id) {
		alerts = alerts.filter(node => node.id != id);
	}
	
	unsubscribe	= alertStore.subscribe(value => {
		if (!value) { return; }
		addAlert(value.message, value.type, value.timeout);
	});
	onDestroy(unsubscribe);
	
</script>

{#if alerts}
<ul 
    data-cy="alert-list" 
    class="fixed top-0 right-0 z-50 w-88 transform translate-y-1"
>
	{#each alerts as alert (alert.id)}
	<li 
        data-cy="alert-item" 
        class="first:mt-0 mt-2 relative" transition:slide|local={{ easing: quintOut }}
    >
		<AlertBlock {...alert} on:close={ (e) => removeAlert(e.detail.id) } />
	</li>
	{/each}
</ul>
{/if}

