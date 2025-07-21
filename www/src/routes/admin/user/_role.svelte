<script>
    import { stores } from '@sapper/app'
    import * as alert from '../../../components/alert/alert.js';
    import Checkbox from '../../../components/input/Checkbox.svelte';
    import validate from 'validate.js';
    import * as UserApi from '@app/api/user';
    import {
        ROLE_USER,
        ROLE_ADMIN,
        ROLE_TIMELINE,
    } from '@app/constants';

    export let model;

    const { session, page } = stores();
    const { token } = $session;

    $: roles = model.getRoles();
    
    async function assign(role) {
        if (model.isNewRecord()) {
            return false;
        }
        if (await model.assign(role) === false) {
            alert.danger(`❗️Unable to assign ${role} role.`);
        }
        model = model;
        return false;
    }

    async function revoke(role) {
        if (model.isNewRecord()) {
            return false;
        }
        if (await model.revoke(role) === false) {
            alert.danger(`❗️Unable to revoke ${role} role.`);
        }
        model = model;
        return false;
    }

    function handleChange(e) {
        if (e.detail.value) {
            assign(e.detail.label);
        } else {
            revoke(e.detail.label);
        }
    }

</script>

<div class="border-blue-200 bg-blue-100 rounded-md text-blue-400 border py-2 px-4 mb-6">
    This page will autosave.
</div>

<div class="mb-6">
    <Checkbox 
        on:change={handleChange}
        checked={model.hasRole(ROLE_USER)} 
        label={ROLE_USER} 
    />
    <p class="text-xs text-gray-500">
        <i class="fas fa-info-circle"></i> 
        Default role, don't uncheck. Actually it kinda do nothing for now.
    </p>
</div>
<div class="mb-6">
    <Checkbox 
        on:change={handleChange}
        checked={model.hasRole(ROLE_TIMELINE)} 
        label={ROLE_TIMELINE} 
    />
    <p class="text-xs text-gray-500">
        <i class="fas fa-info-circle"></i> 
        Add user to timeline calendar.
    </p>
</div>
<div class="mb-6">
    <Checkbox 
        on:change={handleChange}
        checked={model.hasRole(ROLE_ADMIN)} 
        label={ROLE_ADMIN} 
    />
    <p class="text-xs text-gray-500">
        <i class="fas fa-info-circle"></i> 
        Administrator access, don't anyhow.
    </p>
</div>
