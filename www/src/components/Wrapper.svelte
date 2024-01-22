<script>
    export let wrapperClass;
</script>

<div class="p-3 sm:p-6 {wrapperClass ? wrapperClass : ''}">
    <slot></slot>
</div>