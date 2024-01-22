<script context="module">
    import * as UserApi from '@app/api/user';
    import { loginIsValid } from '@app/accessControl';

    export async function preload({ params }, session) {
        const { token } = session;
        const login = await loginIsValid(token);
        if (!login) {
            return this.redirect(302, '/user/logout');
        }
        const id = params.id;
        let user;
        if (id == session.user.id) {
            user = session.user;
        } else {
            try {
                const response = await UserApi.view(id, token);
                if (response.success) {
                    user = response.data;
                }
            } catch (error) {
                this.error(500, error.name + ': ' + error.message);
            }
        }
        return user ? { user } : this.error(404, 'The resquested user is not available.');
    }
</script>

<script>
    import Container from '../../../components/Container.svelte';
    import Wrapper from '../../../components/Wrapper.svelte';
    import PageTitle from '../../../components/PageTitle.svelte';
    import Card from '../../../components/Card.svelte';
    import Button from '../../../components/Button.svelte';
    import DetailedRow from '../../../components/DetailedRow.svelte';
    import TextInput from '../../../components/input/Text.svelte';
    import EmailInput from '../../../components/input/Email.svelte';
    import * as alert from '../../../components/alert/alert.js';
    import { goto, stores } from '@sapper/app';
    import moment from 'moment';
    import validate from 'validate.js';
    import axios from 'axios'

    export let user;

    const { session } = stores();
    const isOwner = user.id === $session.user.id;

    let { email, display_name } = user;
    let isLoading = false;
    let isUpdate = false;

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
        display_name: {
            presence: true,
            type: "string",
            length: {
                minimum: 2
            }
        },
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
                `/profile/update`, 
                { id: user.id, email, display_name }
            ).then(
                response => {
                    isLoading = false; 
                    return response.data;
                }
            );
            if (response.success) {
                user = response.data;
                $session.user = user;
                cancelUpdate();
                alert.success(`🙆 Profile updated`);
                return true;
            } else if (response.status == 422) {
                response.data.forEach((error) => {
                    errors = {
                        ...errors,
                        ...{ [error.field]: error.message }
                    };
                });
                return false;
            } else {
                alert.danger(`❗️Unable to update. Some server error.`);
            }
        } catch (error) {
            alert.danger(error.name + ': ' + error.message);
            isLoading = false;
        }        
    }

    function validateForm() {
        errors = validate({ email, display_name }, rules);
    }

    function valiateField(event) {
        const { name, value, type } = event.detail;
        // not using validate single, have to re-format error message
        const error = validate({ [name]: value }, { [name]: rules[name] }); 
        if (error) {
            errors = { ...errors, ...error };
        } else {
            errors[name] = undefined;
        }
    }

    function cancelUpdate() {
        isUpdate = false;
        isLoading = false;
        display_name = user.display_name;
        email = user.email;
    }

    function enableUpdate() {
        if (isOwner) {
            isUpdate = true;
        } else {
            isUpdate = false;
            alert.danger(`😡 Not your account!`);
        }
    }

</script>

<svelte:head>
    <title>Profile</title>
</svelte:head>

<PageTitle title="Profile">
    {#if isOwner && !isUpdate}
        <div class="">
            <Button 
                size="sm"
                on:click={enableUpdate}
                label="Update"
                type="button"
            />
            <Button 
                size="sm"
                label="Change Password"
                href="/profile/change-password"
                color="gray"
                buttonClass="ml-1"
                isLink={true}
            />
        </div>
    {/if}
</PageTitle>

<Container>
    <Card title="Account Information">
        <Wrapper wrapperClass="pt-0 sm:pt-0">
            <form on:submit|preventDefault={handleSubmit}>

                <DetailedRow>
                    <div slot="label">
                        Name
                    </div>
                    <div slot="content">
                        {#if isUpdate}
                            <TextInput 
                                containerClass="my-auto sm:flex-grow"
                                on:keyup={valiateField}
                                name="display_name"
                                bind:value={display_name}
                                error={errors && errors.display_name}
                                required 
                                minlength="2"
                                placeholder="Peter Tan"
                            />
                        {:else}
                            <span class="my-auto sm:flex-grow">{user.display_name}</span>
                        {/if}
                    </div>
                </DetailedRow>

                <DetailedRow>
                    <div slot="label">
                        Email
                    </div>
                    <div slot="content">
                        {#if isUpdate}
                            <EmailInput 
                                containerClass="my-auto sm:flex-grow"
                                on:keyup={valiateField}
                                name="email"
                                bind:value={email}
                                error={errors && errors.email}
                                required 
                                placeholder="peter.tan@whitecoat.global"
                                type="email"
                            />
                        {:else}
                            <span class="my-auto sm:flex-grow">{user.email}</span>
                        {/if}
                    </div>
                </DetailedRow>

                <DetailedRow>
                    <div slot="label">
                        Registered At
                    </div>
                    <div slot="content">
                        {moment.unix(user.created_at).fromNow()}
                    </div>
                </DetailedRow>

                {#if isUpdate}
                    <div class="mt-6">
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
                            type="button"
                            on:click={cancelUpdate}
                            color="gray"
                        />
                    </div>
                {/if}

            </form>
        </Wrapper>
    </Card>
</Container>