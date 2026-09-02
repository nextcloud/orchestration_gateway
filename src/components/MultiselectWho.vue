<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
  -
  - Adapted from the approval app's MultiselectWho.vue (users-only).
-->
<template>
	<NcSelect
		class="webhook-multiselect"
		:model-value="selectValue"
		:multiple="multiple"
		:loading="loadingSuggestions"
		:options="formattedSuggestions"
		:placeholder="placeholder"
		:clear-search-on-select="true"
		:close-on-select="!multiple"
		:clearable="true"
		:user-select="false"
		:filterable="false"
		:append-to-body="false"
		v-bind="$attrs"
		@search="asyncFind"
		@update:model-value="onUpdate">
		<template #option="option">
			<div class="select-suggestion">
				<NcAvatar
					:user="option.entityId"
					:hide-status="true" />
				<span class="multiselect-name">
					{{ option.displayName }}
				</span>
				<span class="icon icon-user multiselect-icon" />
			</div>
		</template>
		<template #selected-option="option">
			<NcAvatar
				:user="option.entityId"
				:hide-status="true" />
			<span class="multiselect-name">
				{{ option.displayName }}
			</span>
			<span class="icon icon-user multiselect-icon" />
		</template>
		<template #noOptions>
			{{ t('orchestration_gateway', 'No recommendations. Start typing.') }}
		</template>
		<template #noResult>
			{{ t('orchestration_gateway', 'No result.') }}
		</template>
	</NcSelect>
</template>

<script>
import { getCurrentUser } from '@nextcloud/auth'
import { generateOcsUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

import NcAvatar from '@nextcloud/vue/components/NcAvatar'
import NcSelect from '@nextcloud/vue/components/NcSelect'

export default {
	name: 'MultiselectWho',

	components: {
		NcAvatar,
		NcSelect,
	},

	props: {
		value: {
			type: Array,
			required: true,
		},
		multiple: {
			type: Boolean,
			default: true,
		},
		placeholder: {
			type: String,
			default: t('orchestration_gateway', 'Select user'),
		},
	},

	emits: ['update:value'],

	data() {
		return {
			loadingSuggestions: false,
			suggestions: [],
			query: '',
			currentUser: getCurrentUser(),
		}
	},

	computed: {
		selectValue() {
			if (this.multiple) {
				return this.value
			}
			return this.value[0] ?? null
		},
		formattedSuggestions() {
			const result = this.suggestions
				.filter((s) => {
					return (
						s.source === 'users'
						&& !this.value.find((u) => u.type === 'user' && u.entityId === s.id)
					)
				})
				.map((s) => {
					return {
						entityId: s.id,
						type: 'user',
						displayName: s.label,
						id: 'user-' + s.id,
					}
				})

			if (this.currentUser && this.query) {
				const lowerCurrent = this.currentUser.displayName.toLowerCase()
				const lowerQuery = this.query.toLowerCase()
				if (
					lowerCurrent.match(lowerQuery)
					&& !this.value.find(
						(u) => u.type === 'user' && u.entityId === this.currentUser.uid,
					)
				) {
					result.push({
						entityId: this.currentUser.uid,
						type: 'user',
						displayName: this.currentUser.displayName,
						id: 'user-' + this.currentUser.uid,
					})
				}
			}

			return result
		},
	},

	methods: {
		onUpdate(selected) {
			if (this.multiple) {
				this.$emit('update:value', selected ?? [])
				return
			}
			this.$emit('update:value', selected ? [selected] : [])
		},
		asyncFind(query) {
			this.query = query
			if (query === '') {
				this.suggestions = []
				return
			}
			this.loadingSuggestions = true
			const url = generateOcsUrl('core/autocomplete/get', 2).replace(/\/$/, '')
			axios
				.get(url, {
					params: {
						format: 'json',
						search: query,
						itemType: ' ',
						itemId: ' ',
						shareTypes: [0],
					},
				})
				.then((response) => {
					this.suggestions = response.data.ocs.data
				})
				.catch((error) => {
					showError(t('orchestration_gateway', 'Impossible to get user list'))
					console.error(error)
				})
				.then(() => {
					this.loadingSuggestions = false
				})
		},
	},
}
</script>

<style scoped lang="scss">
.webhook-multiselect {
	.multiselect-name {
		flex-grow: 1;
		margin-left: 10px;
		overflow: hidden;
		text-overflow: ellipsis;
	}
	.multiselect-icon {
		opacity: 0.5;
		margin-left: 4px;
	}
	.select-suggestion {
		display: flex;
		align-items: center;
	}
}
</style>
