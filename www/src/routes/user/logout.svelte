<script context="module">
    export async function preload(page, session) {
        if (!session.user) {
            await this.fetch('/auth/logout');
            return this.redirect(302, '/user/login');
        }    
    }
</script>

<script>
    import { goto, stores } from '@sapper/app';
    import axios from 'axios';
    import * as alert from '../../components/alert/alert.js';

    const { session } = stores();
    axios.get('/auth/logout');

    const user = $session.user;

    session.set({
        isGuest: true,
        user: null,
        token: null,
    });

    goto('/user/login').then(alert.info(`👋 Hasta la vista, ${user.display_name}`));

</script>