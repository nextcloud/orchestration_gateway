<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->
<template>
	<div class="authorization-picker">
		<MultiselectWho id="webhook-auth"
			class="authorization-picker__users"
			:value="authUserIds"
			:multiple="true"
			:placeholder="t('orchestration_gateway', 'Include authorization for users…')"
			@update:value="onUsersChange" />
		<div class="authorization-picker__roles">
			<NcCheckboxRadioSwitch :model-value="authIncludeOwner"
				type="checkbox"
				@update:model-value="onOwnerChange">
				{{ t('orchestration_gateway', 'Owner') }}
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch :model-value="authIncludeTrigger"
				type="checkbox"
				@update:model-value="onTriggerChange">
				{{ t('orchestration_gateway', 'Trigger') }}
			</NcCheckboxRadioSwitch>
		</div>
	</div>
</template>

<script>
import NcCheckboxRadioSwitch from '@nextcloud/vue/components/NcCheckboxRadioSwitch'

import MultiselectWho from './MultiselectWho.vue'

export default {
	name: 'AuthorizationPicker',

	components: {
		MultiselectWho,
		NcCheckboxRadioSwitch,
	},

	props: {
		modelValue: {
			type: Object,
			default: undefined,
		},
	},

	emits: ['update:modelValue'],

	computed: {
		authUserIds() {
			const userIds = this.modelValue?.user_ids
			if (!Array.isArray(userIds)) {
				return []
			}
			return userIds.map((uid) => ({
				entityId: uid,
				type: 'user',
				displayName: uid,
				id: 'user-' + uid,
			}))
		},
		authIncludeOwner() {
			return this.modelValue?.user_roles?.includes('owner') ?? false
		},
		authIncludeTrigger() {
			return this.modelValue?.user_roles?.includes('trigger') ?? false
		},
	},

	methods: {
		onUsersChange(users) {
			this.emitTokenNeeded(users, this.authIncludeOwner, this.authIncludeTrigger)
		},
		onOwnerChange(value) {
			this.emitTokenNeeded(this.authUserIds, value, this.authIncludeTrigger)
		},
		onTriggerChange(value) {
			this.emitTokenNeeded(this.authUserIds, this.authIncludeOwner, value)
		},
		emitTokenNeeded(users, includeOwner, includeTrigger) {
			const roles = []
			if (includeOwner) {
				roles.push('owner')
			}
			if (includeTrigger) {
				roles.push('trigger')
			}
			this.$emit('update:modelValue', {
				user_ids: users.map((u) => u.entityId),
				user_roles: roles,
			})
		},
	},
}
</script>

<style scoped lang="scss">
.authorization-picker {
	display: flex;
	flex-direction: column;
	flex-grow: 1;

	&__users {
		width: 100%;
	}

	&__roles {
		display: flex;
		flex-wrap: wrap;
	}
}
</style>
