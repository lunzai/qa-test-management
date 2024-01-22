<script>
    export let passed = 0;
    export let pending = 0;
    export let failed = 0; 
    $: total = passed + pending + failed;
</script>

<div>
    <span>{total} (<span class="text-green-500">{passed}</span>/<span class="text-red-500">{failed}</span>/<span class="text-gray-500">{pending}</span>)</span>
</div>