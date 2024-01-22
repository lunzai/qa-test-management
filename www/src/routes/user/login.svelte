<script context="module">
    export async function preload(page, session) {
        if (session.user || session.token) {
            this.redirect(302, '/');
        }
    }
</script>

<script>
    import EmailInput from '../../components/input/Email.svelte';
    import PasswordInput from '../../components/input/Password.svelte';
    import Button from '../../components/Button.svelte';
    import validate from 'validate.js';
    import axios from 'axios';
    import * as alert from '../../components/alert/alert.js';
    import { goto, stores } from '@sapper/app';

    const { session } = stores();

    

    let email;
    let password;
    let errors = {};
    let user;
    let isLoading = false;

    const rules = {
        email: {
            presence: true,
            email: true,
            type: "string"
        },
        password: {
            presence: true,
            type: "string",
            length: {
                minimum: 8
            }
        }        
    };

    async function handleSubmit(event) {
        validateForm();
        if (errors) {
            return false;
        }
        isLoading = true;    
        try {
            const response = await axios.post(
                '/auth/login', 
                { email, password }
            )
            .then(
                response => { 
                    isLoading = false; 
                    return response.data 
                }
            );
            password = null;
            if (response.success) {
                const { user, token } = response.data;
                session.set({ user, token, isGuest: false });
                alert.success(`🥳 Welcome back, ${user.display_name}.`);
                goto('/');
            } 
            else {
                alert.danger(`❗️Invalid email or password.`);
            }
        } catch (error) {
            alert.danger(error.name + ': ' + error.message);
            isLoading = false;
        }
    }

    function validateForm() {
        errors = validate({ email, password }, rules);
    }

    function valiateField(event) {
        const { name, value, type } = event.detail;
        // not using validate single, have to re-format error message
        const error = validate({ [name]: value }, { [name]: rules[name] });
        if (error) {
            errors = { ...errors, ...error };
        } else {
            errors = { ...errors, ...{ [name]: null } };
        }
    }

</script>

<svelte:head>
    <title>Login</title>
</svelte:head>

<form on:submit|preventDefault={handleSubmit}>
    <div class="grid grid-cols-12 gap-4 mt-20 sm:mt-32">
        <div class="col-start-2 col-span-10 sm:col-start-3 sm:col-span-8 md:col-start-4 md:col-span-6 bg-white shadow-xl rounded-lg">
            <div class="p-6">
                
                <h1 class="text-gray-800 font-medium tracking-wider text-2xl mb-6">Login</h1>

                <EmailInput  
                    on:keyup={valiateField}
                    containerClass="mb-4"
                    name="email"
                    label="Email"
                    bind:value={email}
                    error={errors && errors.email}
                    required 
                    placeholder="peter.tan@whitecoat.global"
                    type="email"
                />

                <PasswordInput  
                    on:keyup={valiateField}
                    containerClass="mb-4"
                    name="password"
                    label="Password"
                    bind:value={password}
                    error={errors && errors.password}
                    required 
                    placeholder="Your Password"
                    type="password"
                />

                <!--                 
                <div>
                    <a
                        class="font-medium text-indigo-500 hover:text-indigo-700"
                        href="user/forget-password"
                    >
                        Forget Password?
                    </a>
                </div> -->

                <div class="mt-4">
                    <Button 
                        label="Login" 
                        type="submit" 
                        color="indigo"
                        isLoading={isLoading} 
                        disabled={!errors}  
                    /> 
                    <Button 
                        buttonClass="ml-2"
                        label="Register"  
                        color="gray"
                        href="/user/register"
                        isLink={true} 
                    />
                </div>

            </div>
        </div>
    </div>
</form>
