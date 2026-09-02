<!--
  - SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<form class="webhook-edit">
		<p>
			<label for="webhook-event">{{ t('orchestration_gateway', 'Event') }}</label>
			<NcSelect v-model="localWebhook.event"
				class="webhook-event"
				:options="availableEvents"
				required />
		</p>
		<p>
			<label for="webhook-schema-url">{{ t('orchestration_gateway', 'Schema URL') }}</label>
			<input id="webhook-schema-url"
				v-model="schemaUrl"
				type="text">
		</p>
		<NcButton variant="secondary"
			:text="t('orchestration_gateway', 'Send schema request')"
			:disabled="!schemaUrl || !localWebhook.event"
			@click="sendSchema" />
		<p>
			<label for="webhook-uri">{{ t('orchestration_gateway', 'Trigger URL') }}</label>
			<input id="webhook-uri"
				v-model="localWebhook.uri"
				type="text"
				required>
		</p>
		<p>
			<label for="webhook-event-filter">{{ t('orchestration_gateway', 'Event Filter') }}</label>
			<input id="webhook-event-filter"
				v-model="localWebhook.eventFilterJson"
				type="text">
		</p>
		<div class="webhook-edit__row">
			<label for="webhook-user-filter">{{ t('orchestration_gateway', 'User ID Filter') }}</label>
			<MultiselectWho id="webhook-user-filter"
				class="webhook-user-filter"
				:value="userIdFilterSelection"
				:multiple="false"
				:placeholder="t('orchestration_gateway', 'Filter by user')"
				@update:value="userIdFilterSelection = $event" />
		</div>
		<div class="webhook-edit__row">
			<label for="webhook-auth">{{ t('orchestration_gateway', 'Include authorization for users') }}</label>
			<AuthorizationPicker v-model="localWebhook.tokenNeeded" />
		</div>

		<div class="webhook-edit--footer">
			<NcButton @click="$emit('cancel-form')">
				{{ t('orchestration_gateway', 'Cancel') }}
			</NcButton>
			<NcButton variant="primary"
				:disabled="!localWebhook.event || !localWebhook.uri"
				@click="submitForm">
				<template #icon>
					<CheckIcon :size="20" />
				</template>
				{{ submitText }}
			</NcButton>
		</div>
	</form>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcSelect from '@nextcloud/vue/components/NcSelect'

import axios from '@nextcloud/axios'

import AuthorizationPicker from './AuthorizationPicker.vue'
import MultiselectWho from './MultiselectWho.vue'

export default {
	name: 'BudibaseForm',
	components: {
		AuthorizationPicker,
		MultiselectWho,
		NcButton,
		CheckIcon,
		NcSelect,
	},
	props: {
		submitText: {
			type: String,
			default: t('orchestration_gateway', 'Submit'),
		},
		update: {
			type: Boolean,
			default: false,
		},
		webhook: {
			type: Object,
			required: true,
		},
	},
	emits: [
		'cancel-form',
		'submit',
	],
	data() {
		return {
			availableEvents: loadState('orchestration_gateway', 'webhook-events'),
			localWebhook: null,
			schemaUrl: null,
			userIdFilterSelection: [],
		}
	},
	created() {
		this.localWebhook = {
			...this.webhook,
			eventFilterJson: JSON.stringify(this.webhook.eventFilter),
			headers: { 'Content-Type': 'application/json' },
		}
		if (this.webhook.userIdFilter) {
			this.userIdFilterSelection = [{
				entityId: this.webhook.userIdFilter,
				type: 'user',
				displayName: this.webhook.userIdFilter,
				id: 'user-' + this.webhook.userIdFilter,
			}]
		}
	},
	methods: {
		submitForm() {
			this.$emit('submit', {
				...this.localWebhook,
				userIdFilter: this.userIdFilterSelection[0]?.entityId ?? '',
			})
		},

		async sendSchema() {
			const url = generateUrl('/apps/orchestration_gateway/send-schema')
			try {
				await axios.post(url, { url: this.schemaUrl, event: this.localWebhook.event })
				showSuccess(t('orchestration_gateway', 'Schema request sent'))
			} catch (error) {
				console.error(error)
				showError(
					t('integration_openai', 'Failed to send schema request'),
					{ timeout: 10000 },
				)
			}
		},
	},
}
</script>

<style scoped lang="scss">
.webhook-edit {
	&--footer {
		display: flex;
		justify-content: right;
		padding: 8px 0;
		position: sticky;
		bottom: 0;
		background-color: var(--color-main-background);
		> * {
			margin: 0 4px;
		}
	}

	.settings-hint {
		display: flex;
		align-items: center;
		margin: 0;

		.icon {
			margin-right: 8px;
		}
	}

	.warning-hint {
		margin-left: 160px;
		background-color: var(--color-background-dark);
	}

	p, .webhook-edit__row {
		display: flex;
		align-items: center;
		width: 100%;
		label {
			width: 160px;
			display: inline-block;
		}

		input[type=text] {
			min-width: 200px;
			flex-grow: 1;
		}
		.italic-placeholder::placeholder {
			font-style: italic;
		}
	}

	.webhook-event,
	.webhook-user-filter {
		flex-grow: 1;
	}
}
</style>
