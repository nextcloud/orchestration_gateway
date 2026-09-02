<template>
	<div id="orchestration_prefs" class="section">
		<h2>
			<FlowchartSymbol class="icon" />
			{{ t('orchestration_gateway', 'Orchestration Gateway') }}
		</h2>
		<NcSettingsSection
			:name="t('orchestration_gateway', 'Webhook Listeners')"
			:description="t('orchestration_gateway', 'Manage webhooks to orchestrate external workflows, triggering them when events in Nextcloud occur')"
			:doc-url="webhooksDocUrl">
			<div class="add__buttons">
				<NcButton variant="secondary"
					:text="t('orchestration_gateway', 'Register new webhook')"
					@click="showNewWebhook = true">
					<template #icon>
						<PlusIcon :size="20" />
					</template>
				</NcButton>
				<NcButton variant="secondary"
					:text="t('orchestration_gateway', 'Register new Budibase webhook')"
					@click="showNewBudibase = true">
					<template #icon>
						<PlusIcon :size="20" />
					</template>
				</NcButton>
			</div>

			<NcModal v-if="showNewWebhook"
				size="large"
				:name="t('orchestration_gateway', 'Register a new webhook')"
				:no-close="true">
				<div class="webhookmodal__wrapper">
					<h3>{{ t('orchestration_gateway', 'Register a new webhook') }}</h3>
					<SettingsForm :webhook="newWebhook" @submit="onSubmit" @cancel-form="showNewWebhook=false" />
				</div>
			</NcModal>

			<NcModal v-if="showNewBudibase"
				size="large"
				:name="t('orchestration_gateway', 'Register a new Budibase webhook')"
				:no-close="true">
				<div class="webhookmodal__wrapper">
					<h3>{{ t('orchestration_gateway', 'Register a new Budibase webhook') }}</h3>
					<BudibaseForm :webhook="newWebhook" @submit="onSubmit" @cancel-form="showNewBudibase=false" />
				</div>
			</NcModal>

			<v-table v-if="webhookListeners.length > 0" class="webhooks__table">
				<thead>
					<tr>
						<th>
							{{ t('orchestration_gateway', 'ID') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'Event') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'HTTP Method') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'URI') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'Event Filter') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'User ID Filter') }}
						</th>
						<th>
							{{ t('orchestration_gateway', 'Include authorization for users') }}
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
						:event-filter="webhook.eventFilter"
						:user-id-filter="webhook.userIdFilter"
						:token-needed="webhook.tokenNeeded"
						@edit="updateWebhook(webhook)"
						@delete="deleteWebhook(webhook)" />
				</tbody>
			</v-table>

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
import BudibaseForm from './BudibaseForm.vue'

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
		BudibaseForm,
		NcButton,
	},

	props: [],

	data() {
		return {
			webhookListeners: loadState('orchestration_gateway', 'webhook-listeners'),
			webhooksDocUrl: loadState('orchestration_gateway', 'webhooksDocUrl'),
			showNewWebhook: false,
			showNewBudibase: false,
			editWebhook: null,
			newWebhook: {
				id: '',
				uri: '',
				httpMethod: 'POST',
				event: '',
				eventFilter: undefined,
				userIdFilter: '',
				tokenNeeded: undefined,
				headers: undefined,
			},
		}
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
		async onSubmit(webhook) {
			await confirmPassword()
			console.debug('Add new webhook', { data: webhook })
			try {
				webhook.eventFilter = webhook.eventFilterJson ? JSON.parse(webhook.eventFilterJson) : []
			} catch (error) {
				console.error('Could not register a webhook: event filter is no valid JSON', { error })
				showError(t('orchestration_gateway', 'Could not register webhook: event filter is no valid JSON'))
				return
			}
			const url = generateOcsUrl('/apps/webhook_listeners/api/v1/webhooks')
			try {
				const response = await axios.post(url, webhook)

				this.webhookListeners.push(response.data.ocs.data)

				this.showNewWebhook = false
				this.showNewBudibase = false
			} catch (error) {
				console.error('Could not register a webhook: ' + error.message, { error })
				showError(t('orchestration_gateway', 'Could not register webhook:') + ' ' + (error.response?.data?.ocs?.data?.message ?? error.message))
			}
		},
		updateWebhook(webhook) {
			this.editWebhook = { ...webhook }
		},
		async onUpdate(webhook) {
			await confirmPassword()
			console.debug('Update webhook', { data: webhook })

			try {
				webhook.eventFilter = webhook.eventFilterJson ? JSON.parse(webhook.eventFilterJson) : []
			} catch (error) {
				console.error('Could not update a webhook: event filter is no valid JSON', { error })
				showError(t('orchestration_gateway', 'Could not update webhook: event filter is no valid JSON'))
				return
			}
			const url = generateOcsUrl('/apps/webhook_listeners/api/v1/webhooks/{id}', { id: webhook.id })
			try {
				await axios.post(url, webhook)
				this.editWebhook = null
				const index = this.webhookListeners.findIndex((p) => p.id === webhook.id)
				this.webhookListeners[index] = webhook
			} catch (error) {
				console.error('Could not update the webhook: ' + error.message, { error })
				showError(t('orchestration_gateway', 'Could not update the webhook:') + ' ' + (error.response?.data?.ocs?.data?.message ?? error.message))
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
		max-width: 50%;
		border-collapse: collapse;
		table-layout: fixed;

		th, td {
			overflow: hidden;
			padding: 5px 10px 5px 10px;
			text-wrap: wrap;
			overflow-wrap: break-word;
		}

		tbody tr {
			border-top: 1px solid var(--color-border);
		}

		// the action column only needs to have the button size
		th:nth-of-type(8), td:nth-of-type(8) {
			width: calc(2 * var(--default-clickable-area) + 3 * 5px);
		}
	}

.add__buttons {
	display: flex;
	gap: 5px;
}

.webhookmodal__wrapper {
	margin: 20px;
}
</style>
