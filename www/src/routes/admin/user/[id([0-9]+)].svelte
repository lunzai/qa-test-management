<script context="module">
    import UserModel from '@app/models/User';
    import * as userApi from '@app/api/user';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, { user, token }) {
        if (!user.is_admin) {
            return this.error(400, '🤬 Unauthorized access, you are not supposed to be here!')
        }
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        try {
            const model = new UserModel({ token });
            const result = await model.find(params.id);

            if (!result) {
                return this.error(500, '🙀 Oh no. Something wrong?!');
            }
            return { model }
        } catch (error) {
            this.error(500, error.name + ': ' + error.message);
        }
    }
</script>

<script>
    import Container from '../../../components/Container.svelte';
    import Card from '../../../components/Card.svelte';
    import Wrapper from '../../../components/Wrapper.svelte';
    import AccountTab from './_account.svelte';
    import PasswordTab from './_password.svelte';
    import RoleTab from './_role.svelte';
    
    export let model;
    let activeTab = 'account';
</script>

<svelte:head>
	<title>Admin: User - Update {model.get('display_name')}</title>
</svelte:head>

<Container>
    <Card>
        <Wrapper>
            <div class="mb-10">
                <ul class="rounded-md flex flex-wrap pl-0">
                    <li>
                        <button 
                            on:click={() => { activeTab = 'account' }}
                            class="px-4 py-2 mr-3 text-gray-900 focus:outline-none 
                            {activeTab == 'account' ? 'text-indigo-500 border-b-2 border-indigo-500':''}"
                        >
                            Account Information
                        </button>
                        <button 
                            on:click={() => { activeTab = 'password' }}
                            class="px-4 py-2 mr-3 text-gray-900 focus:outline-none 
                            {activeTab == 'password' ? 'text-indigo-500 border-b-2 border-indigo-500':''}"
                        >
                            Change Password
                        </button>
                        <button 
                            on:click={() => { activeTab = 'role' }}
                            class="px-4 py-2 mr-3 text-gray-900 focus:outline-none 
                            {activeTab == 'role' ? 'text-indigo-500 border-b-2 border-indigo-500':''}"
                        >
                            Roles & Permissions
                        </button>
                    </li>
                </ul>
            </div>
            <div>
                {#if activeTab == 'account'}
                    <div>
                        <AccountTab {model} />
                    </div>
                {/if}
                {#if activeTab == 'password'}
                    <div>
                        <PasswordTab {model} />
                    </div>
                {/if}
                {#if activeTab == 'role'}
                    <div>
                        <RoleTab {model} />
                    </div>
                {/if}
            </div>


        </Wrapper>
    </Card>
</Container>






