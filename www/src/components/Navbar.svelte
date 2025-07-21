<script>
    
    export let user;
    let isOpenUserMenu = false;

    const guestLinks = [
        {
            label: 'Login',
            href: '/user/login',
        },
        {
            label: 'Signup',
            href: '/user/register',
        }
    ];

    const userLinks = [
        {
            label: 'Profile',
            href: '/profile',
        },
        {
            label: 'Change Password',
            href: '/profile/change-password',
        },
        {
            label: 'Logout',
            href: '/user/logout',
        }
    ];

    const navLinks = [
        {
            label: 'Home',
            href: '/',
        },
        {
            label: 'Timeline',
            href: '/timeline',
        },
        {
            label: 'Group',
            href: '/group',
        },
        {
            label: 'My Issues',
            href: '/issue/assigned',
        },
        {
            label: 'Bug Report',
            href: '/bug',
        },
        {
            label: 'Admin',
            href: '/admin',
            visible: user.is_admin
        },
    ];

</script>

<nav data-cy="main-menu" class="bg-white shadow-xl mb-6 sm:m-6 sm:rounded-lg">
    <div class="wrapper">
        <div class="flex items-stretch justify-between">
            <div class="flex items-center flex-grow px-1 sm:px-3">
                {#if navLinks}
                    {#each navLinks as { label, href, visible }, i}
                        {#if visible !== false}
                            <a 
                                {href}
                                rel=prefetch
                                class="px-2 sm:px-3 font-medium tracking-wide uppercase text-gray-700 hover:text-indigo-600 transition-colors duration-200"
                            >
                                {label}
                            </a>
                        {/if}
                    {/each}
                {/if}
            </div>
            <div class="relative">
                <button 
                    data-cy="user-menu-trigger"
                    on:click={() => { isOpenUserMenu = !isOpenUserMenu }}
                    class="relative z-10 flex items-center py-4 pr-3 sm:pr-6 focus:outline-none"
                >
                    <div class="mr-4 z-10 font-medium tracking-wide uppercase text-gray-700 hover:text-indigo-600 hidden sm:block">
                        {user.display_name}
                    </div> 
                    <div class="relative z-10 overflow-hidden h-10 w-10 rounded-full shadow">
                        <img class="h-full w-full object-cover" src="/images/default-avatar.svg" alt="{user.display_name}" />
                    </div>
                </button>
                {#if isOpenUserMenu && userLinks}
                    <button 
                        data-cy="user-menu-screen"
                        on:click={() => { isOpenUserMenu = false }} 
                        tabindex="-1" 
                        class="fixed inset-0 h-full w-full bg-black opacity-25 cursor-default z-10"
                    ></button>
                    <div
                        data-cy="user-menu" 
                        class="absolute right-0 -mt-2 border border-solid border-gray-200 shadow-xl bg-white rounded-lg w-48 py-2 z-20" style="right:1.5em"
                    >
                        {#each userLinks as { label, href }, i}
                            <a 
                                data-cy="user-menu-item"
                                on:click={() => { isOpenUserMenu = false; }}
                                {href}
                                class:mt-1={i > 0}                                 
                                class="block py-2 px-5 mt-1 hover:bg-indigo-600 hover:text-white transition-colors duration-200"
                            >
                                {label}
                            </a>
                        {/each}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</nav>