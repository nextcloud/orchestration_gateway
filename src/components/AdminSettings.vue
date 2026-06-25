<template>
	<div id="orchestration_prefs" class="section">
		<h2>
			<FlowchartSymbol class="icon" />
			{{ t('orchestration_gateway', 'Orchestration Gateway') }}
		</h2>
		<NcSettingsSection
			:name="t('orchestration_gateway', 'Webhook Listeners')"
			:description="t('orchestration_gateway', 'here description of webhook listeners')">
			<NcButton variant="secondary"
				:text="t('orchestration_gateway', 'Register new webhook')"
				@click="showNewWebhook = true">
				<template #icon>
					<PlusIcon :size="20" />
				</template>
			</NcButton>

			<NcModal v-if="showNewWebhook"
				size="large"
				:name="t('orchestration_gateway', 'Register a new webhook')"
				:no-close="true">
				<div class="webhookmodal__wrapper">
					<h3>{{ t('orchestration_gateway', 'Register a new webhook') }}</h3>
					<p class="settings-hint">
						{{ t('orchestration_gateway', 'Configure your webhook to redirect back to {url}', { url: "test" }) }}
					</p>
					<SettingsForm :webhook="newWebhook" @submit="onSubmit" @cancel-form="showNewWebhook=false" />
				</div>
			</NcModal>

			<table v-if="webhookListeners.length > 0" class="webhooks__table">
				<thead>
					<tr>
						<th>
							{{ t('orchestration_gateway', 'ID') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'URI') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'Event') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'HTTP Method') }}
						</th>
						<th>
							<span class="hidden-visually">{{ t('orchestration_gateway', 'Update or delete listener') }}</span>
						</th>
					</tr>
				</thead>
				<tbody>
					<Webhook
						v-for="webhook in webhookListeners"
						:id="webhook.id"
						:key="webhook.id"
						:uri="webhook.uri"
						:event="webhook.event"
						:method="webhook.httpMethod"
						@edit="updateWebhook(webhook)"
						@delete="deleteWebhook(webhook)" />
				</tbody>
			</table>

			<NcModal v-if="editWebhook"
				size="large"
				:name="t('orchestration_gateway', 'Update webhook settings')"
				:no-close="true">
				<div class="webhookmodal__wrapper">
					<h3>{{ t('orchestration_gateway', 'Update webhook settings') }}</h3>
					<SettingsForm :webhook="editWebhook"
						:update="true"
						:submit-text="t('orchestration_gateway', 'Update webhook')"
						@submit="onUpdate"
						@cancel-form="editWebhook = null" />
				</div>
			</NcModal>
		</NcSettingsSection>
	</div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import NcSettingsSection from '@nextcloud/vue/components/NcSettingsSection'
import NcModal from '@nextcloud/vue/components/NcModal'
import NcButton from '@nextcloud/vue/components/NcButton'
import { generateOcsUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import { confirmPassword } from '@nextcloud/password-confirmation'
import axios from '@nextcloud/axios'

import Webhook from './Webhook.vue'
import SettingsForm from './SettingsForm.vue'

import PlusIcon from 'vue-material-design-icons/Plus.vue'
import FlowchartSymbol from './icons/FlowchartIcon.vue'

export default {
	name: 'AdminSettings',

	components: {
		FlowchartSymbol,
		PlusIcon,
		Webhook,
		NcSettingsSection,
		NcModal,
		SettingsForm,
		NcButton,
	},

	props: [],

	data() {
		return {
			webhookListeners: loadState('orchestration_gateway', 'webhook-listeners'),
			showNewWebhook: false,
			editWebhook: null,
			newWebhook: {
				id: '',
				uri: '',
				httpMethod: '',
				event: '',
			},
		}
	},

	computed: {
	},

	mounted() {
	},

	methods: {
		async deleteWebhook(webhook) {
			await confirmPassword()
			console.debug('Remove webhook', { webhook })
			const url = generateOcsUrl('/apps/webhook_listeners/api/v1/webhooks/{id}', {
				id: webhook.id,
			})

			try {
				await axios.delete(url)

				this.webhookListeners = this.webhookListeners.filter(p => p.id !== webhook.id)
			} catch (error) {
				console.error('Could not remove a webhook: ' + error.message, { error })
				showError(t('orchestration_gateway', 'Could not remove webhook: {msg}', { msg: error.message }))
			}
		},
		async onSubmit() {
			await confirmPassword()
			console.debug('Add new webhook', { data: this.newWebhook })

			const url = generateOcsUrl('/apps/webhook_listeners/api/v1/webhooks')
			try {
				const response = await axios.post(url, this.newWebhook)

				this.webhookListeners.push(response.data.ocs.data)

				this.newWebhook.id = ''
				this.newWebhook.uri = ''
				this.newWebhook.httpMethod = ''
				this.newWebhook.event = ''
				this.showNewWebhook = false
			} catch (error) {
				console.error('Could not register a webhook: ' + error.message, { error })
				showError(t('user_oidc', 'Could not register webhook:') + ' ' + (error.response?.data?.ocs?.data?.message ?? error.message))
			}
		},
		updateWebhook(webhook) {
			this.editWebhook = { ...webhook }
		},
		async onUpdate(webhook) {
			await confirmPassword()
			console.debug('Update webhook', { data: webhook })

			const url = generateOcsUrl('/apps/webhook_listeners/api/v1/webhooks/{id}', { id: webhook.id })
			try {
				await axios.post(url, webhook)
				this.editWebhook = null
				const index = this.webhookListeners.findIndex((p) => p.id === webhook.id)
				this.webhookListeners[index] = webhook
			} catch (error) {
				console.error('Could not update the webhook: ' + error.message, { error })
				showError(t('user_oidc', 'Could not update the webhook:') + ' ' + (error.response?.data?.ocs?.data?.message ?? error.message))
			}
		},
	},
}
</script>

<style scoped lang="scss">
#orchestration_prefs {
	h2 {
		display: flex;
		align-items: center;
		justify-content: start;
		gap: 8px;
	}
}

.webhooks__table {
		width: 100%;
		border-collapse: collapse;
		table-layout: fixed;

		th, td {
			overflow: hidden;
			padding: var(--default-grid-baseline);
			text-wrap: wrap;
			overflow-wrap: break-word;
		}

		tbody tr {
			border-top: 1px solid var(--color-border);
		}

		th:nth-of-type(2), td:nth-of-type(2) {
			width: 40%;
		}

		th:nth-of-type(3), td:nth-of-type(3) {
			width: 40%;
		}

		th:nth-of-type(4), td:nth-of-type(4) {
			width: 10%;
		}

		// the action column only needs to have the button size
		th:nth-of-type(5), td:nth-of-type(5) {
			width: calc(2 * var(--default-clickable-area) + 3 * 5px);
		}
	}

.webhookmodal__wrapper {
	margin: 20px;
}
</style>
