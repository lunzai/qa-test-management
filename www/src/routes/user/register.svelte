<script context="module">
    export async function preload(page, session) {
        if (session.user) {
            return this.redirect(302, '/');
        }    
    }
</script>

<script>
// TODO: Use card component
import TextInput from '../../components/input/Text.svelte';
import EmailInput from '../../components/input/Email.svelte';
import PasswordInput from '../../components/input/Password.svelte';
import Button from '../../components/Button.svelte';
import validate from 'validate.js';
import axios from 'axios'
import * as alert from '../../components/alert/alert.js';
import { goto, stores } from '@sapper/app';

const { session } = stores();

let email;
let password;
let display_name;
let isLoading = false;

const rules = {
    email: {
        presence: true,
        email: true,
        type: "string",
        format: {
            pattern: /.*@whitecoat\.(global|com\.sg)/i,
            message: `not allowed`,
        }
    },
    password: {
        presence: true,
        type: "string",
        length: {
            minimum: 8
        }
    },
    display_name: {
        presence: true,
        type: "string",
        length: {
            minimum: 2
        }
    }
};
let errors = {};

async function handleSubmit() {
    validateForm();
    if (errors) {
        return false;
    }
    isLoading = true;    
    try {
        const response = await axios.post(
            '/auth/register', 
            { email, password, display_name }
        )
        .then(
            response => { 
                isLoading = false; 
                return response.data 
            }
        );
        if (response.success) {
            const { user, token } = response.data;
            $session.user = user;
            $session.token = token;
            email = null;
            password = null;
            display_name = null;
            goto('/').then(
                alert.success(`🥳 Welcome, ${user.display_name}. OK, now go do QA!`)
            );
            return true;
        } 
        else if (response.status = 422) {
            response.data.forEach((error) => {
                errors = {
                    ...errors,
                    ...{ [error.field]: error.message }
                };
            });
            return false;
        }
        else {
            alert.danger(`❗️Unable to register. Some server error.`);
        }
    } catch (error) {
        alert.danger(error.name + ': ' + error.message);
        isLoading = false;
    }    
}

function validateForm() {
    errors = validate({ email, password, display_name }, rules);
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
    <title>Register</title>
</svelte:head>

<form on:submit|preventDefault={handleSubmit}>
    <div class="grid grid-cols-12 gap-4 mt-20 sm:mt-24">
        <div class="col-start-2 col-span-10 sm:col-start-3 sm:col-span-8 md:col-start-4 md:col-span-6 bg-white shadow-xl rounded-lg">
            <div class="p-6">
                
                <h1 class="text-gray-800 font-medium tracking-wider text-2xl mb-6">Register</h1>

                <TextInput  
                    on:keyup={valiateField}
                    containerClass="mb-4"
                    name="display_name"
                    label="Name"
                    bind:value={display_name}
                    error={errors && errors.display_name}
                    required 
                    minlength="2"
                    placeholder="Peter Tan"
                />

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
                    placeholder="Some top secret password"
                    type="password"
                />

                <div class="mt-6">
                    <Button 
                        label="Register" 
                        type="submit" 
                        color="indigo"
                        isLoading={isLoading} 
                        disabled={!errors}  
                    /> 
                    <Button 
                        buttonClass="ml-2"
                        label="Cancel"  
                        color="gray"
                        href="/user/login"
                        isLink={true} 
                    />
                </div>

            </div>
        </div>
    </div>
</form>

