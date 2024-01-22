<script>
    export let paddingTop = 2;
    export let paddingBottom = 3;
    export let smCols = 3;
    export let mdCols = 4;
    export let lgCols = 5;
    export let smLeftCol = 1;
    export let mdLeftCol = 1;
    export let lgLeftCol = 1;

    const smRightCol = parseInt(smCols, 10) - parseInt(smLeftCol, 10);
    const mdRightCol = parseInt(mdCols, 10) - parseInt(mdLeftCol, 10);
    const lgRightCol = parseInt(lgCols, 10) - parseInt(lgLeftCol, 10);
</script>


<div data-cy="detailed-view" class="flex flex-wrap">
    <div class="w-full sm:w-{smLeftCol}/{smCols} md:w-{mdLeftCol}/{mdCols} lg:w-{lgLeftCol}/{lgCols} flex flex-col">
        <div data-cy="detailed-label" class="pt-{paddingTop} sm:pb-{paddingBottom} flex items-stretch justify-start font-semibold">
            <slot name="label"></slot>
        </div>
    </div>
    <div class="w-full sm:w-{smRightCol}/{smCols} md:w-{mdRightCol}/{mdCols} lg:w-{lgRightCol}/{lgCols}">
        <div data-cy="detailed-content" class="sm:pt-{paddingTop} pb-{paddingBottom}">
            <slot name="content"></slot>
        </div>
    </div>
</div>

