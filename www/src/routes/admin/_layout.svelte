<script>
    import Container from '../../components/Container.svelte';
    export let segment;
    $: nav = [
        {
            label: 'User',
            href: '/admin/user',
            active: segment == 'user',
        },
        // {
        //     label: 'Country',
        //     href: '/admin/country',
        //     active: segment == 'country',
        // },
        // {
        //     label: 'Holiday',
        //     href: '/admin/holiday',
        //     active: segment == 'holiday',
        // },
    ]
</script>

<Container>
    <ul class="mb-6 rounded-md flex flex-wrap pl-0">
        {#each nav as { href, label, active }, i}
        <li>
            <a 
                class="rounded px-4 py-2 mr-3 {active ? 'bg-indigo-500 text-white' : 'text-black'}"
                {href} 
                title={label} 
                alt={label}
                rel=prefetch
            >
                {label}
            </a>
        </li>
        {/each}
    </ul>
</Container>

<slot></slot>
