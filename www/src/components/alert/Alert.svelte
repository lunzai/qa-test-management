<script>
	import { createEventDispatcher } from 'svelte';
	const dispatch = createEventDispatcher();
	
	export let message;
	export let type;
	export let id;
    export let timeout;
    
    const colorList = {
        success: 'green',
        danger: 'red',
        info: 'blue',
        default: 'gray',
        warning: 'orange',
        primary: 'indigo',
        secondary: 'teal'
    }
    
    let color = colorList.hasOwnProperty(type) ? colorList[type] : colorList.default;

	function close() {
        dispatch('close', { id: id });
	}
</script>

<style>
	.alert-progress-bar { 
		animation-name: alert-progress-bar-countdown;
		animation-iteration-count: 1;
		animation-timing-function: linear;
		animation-fill-mode: both;
	}
	@keyframes alert-progress-bar-countdown {
		from { width: 100% }
		to { width: 0% }
	}
</style>

<div 
    data-cy="alert-{type}" 
    class="bg-{color}-100 text-{color}-700 text-sm transition-all duration-300 
    ease-in shadow hover:shadow-lg rounded-lg transform -translate-x-2 
    hover:-translate-x-3 overflow-hidden"
>
	<div
        data-cy="alert-message"  
        class="p-3 pr-8"
    >
		{message}
	</div>
	<button 
        data-cy="alert-close" 
        on:click|once={close} 
        class="absolute right-0 top-0 bottom-0 px-2 mt-0 text-lg hover:bg-{color}-200"
    >
        &times;
    </button>
	{#if timeout}
        <div class="alert-progress-bar h-1 bg-{color}-200" style="animation-duration: {timeout}ms"></div>	
	{/if}
</div>