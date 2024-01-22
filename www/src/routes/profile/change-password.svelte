<script context="module">
    import { loginIsValid } from '@app/accessControl';
    export async function preload({ params }, { token }) {
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
    }
</script>

<script>
    import Container from '../../components/Container.svelte';
    import Wrapper from '../../components/Wrapper.svelte';
    import PageTitle from '../../components/PageTitle.svelte';
    import TextInput from '../../components/input/Text.svelte';
    import EmailInput from '../../components/input/Email.svelte';
    import PasswordInput from '../../components/input/Password.svelte';
    import Button from '../../components/Button.svelte';
    import Card from '../../components/Card.svelte';
    import * as alert from '../../components/alert/alert.js';
    import validate from 'validate.js';
    import axios from 'axios'
    
    import { goto, stores } from '@sapper/app';

    const { session } = stores();
    let user = $session.user;
    let currentPassword = '';
    let newPassword = '';
    let confirmPassword = '';
    let isLoading = false;

    let errors = {};

    const rules = {
        currentPassword: {
            presence: true,
            type: "string",
            length: {
                minimum: 8
            }
        },
        newPassword: {
            presence: true,
            type: "string",
            length: {
                minimum: 8
            },
            equality: {
                attribute: "currentPassword",
                message: "is same as current password",
                comparator: (a, b) => {
                    return a !== b;
                }
            }
        },
        confirmPassword: {
            presence: true,
            type: "string",
            length: {
                minimum: 8
            },
            equality: {
                attribute: "newPassword",
                comparator: (a, b) => {
                    return a === b;
                }
            }
        },
    };

    async function handleSubmit() {
        validateForm();
        if (errors) {
            return false;
        }
        isLoading = true;        
        try {
            const response = await axios.post(
                '/profile/change-password', 
                { id: user.id, currentPassword, newPassword }
            )
            .then(
                response => { 
                    isLoading = true;
                    return response.data;
                }
            );
            if (response.success) {
                goto('/profile').then(alert.success(`㊙️ Password updated!`));
                return true;
            } 
            else if (response.status == 422) { // validate failed on server side
                // hardcode to match backend attribute name
                response.data.forEach((error) => {
                    if (error.field == 'password_current') {
                        errors = {
                            ...errors,
                            ...{ currentPassword: error.message }
                        };
                    }
                    if (error.field == 'password_new') {
                        errors = {
                            ...errors,
                            ...{ newPassword: error.message }
                        };
                    }
                });
                return false;
            } 
            else {
                alert.danger(`❗️Unable to change password. Some server error.`);
            }
        } catch (error) {
            alert.danger(error.name + ': ' + error.message);
            isLoading = false;
        }        
    }

    function validateForm() {
        errors = validate({ currentPassword, newPassword, confirmPassword }, rules);
    }

</script>

<svelte:head>
    <title>Change Password</title>
</svelte:head>
    
<PageTitle title="Profile"></PageTitle>

<Container>
    <Card title="Change Password">
        <Wrapper>

            <form on:submit|preventDefault={handleSubmit} class="">

                <PasswordInput  
                    on:keyup={validateForm}
                    containerClass="mb-6"
                    name="currentPassword"
                    label="Current Password"
                    bind:value={currentPassword}
                    error={errors && errors.currentPassword}
                    required 
                    placeholder="********"
                    type="password"
                />

                <PasswordInput  
                    on:keyup={validateForm}
                    containerClass="mb-6"
                    name="newPassword"
                    label="New Password"
                    bind:value={newPassword}
                    error={errors && errors.newPassword}
                    required 
                    placeholder="********"
                    type="password"
                />

                <PasswordInput  
                    on:keyup={validateForm}
                    containerClass="mb-6"
                    name="confirmPassword"
                    label="Confirm New Password"
                    bind:value={confirmPassword}
                    error={errors && errors.confirmPassword}
                    required 
                    placeholder="********"
                    type="password"
                />

                <div>

                    <Button 
                        label="Save" 
                        type="submit" 
                        color="green"
                        isLoading={isLoading} 
                        disabled={!errors}  
                    />

                    <Button 
                        buttonClass="ml-2"
                        label="Cancel" 
                        href="/profile" 
                        color="gray"
                        isLink={true}
                    />

                </div>
            </form>

        </Wrapper>
    </Card>
</Container>