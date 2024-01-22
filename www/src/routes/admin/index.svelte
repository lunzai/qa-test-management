<script context="module">
    export async function preload(page, session) {
        return this.redirect(302, '/admin/user');  
    }
</script>

<script>
    import { goto, stores } from '@sapper/app';
    goto('/admin/user');
</script>