<script context="module">
    import { loginIsValid } from '@app/accessControl';
    export async function preload({ params }, { user, token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        return this.redirect(302, `/profile/${user.id}`);
    }
</script>