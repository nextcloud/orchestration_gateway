<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<tr>
		<td>{{ id }}</td>
		<td>
			<code class="webhook__code">{{ uri }}</code>
		</td>
		<td>
			<code class="webhook__code">{{ event }}</code>
		</td>
		<td>
			{{ method }}
		</td>
		<td class="update-delete">
			<NcButton
				:aria-label="t('orchestration_gateway', 'Edit')"
				:title="t('orchestration_gateway', 'Edit')"
				@click="$emit('edit')">
				<template #icon>
					<PencilOutlineIcon :size="20" />
				</template>
			</NcButton>
			<NcButton
				:aria-label="t('orchestration_gateway', 'Delete')"
				:title="t('orchestration_gateway', 'Delete')"
				variant="error"
				@click="$emit('delete')">
				<template #icon>
					<IconTrashCanOutline :size="20" />
				</template>
			</NcButton>
		</td>
	</tr>
</template>

<script>

import NcButton from '@nextcloud/vue/components/NcButton'
import IconTrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue'
import PencilOutlineIcon from 'vue-material-design-icons/PencilOutline.vue'

export default {
	name: 'Webhook',

	components: {
		IconTrashCanOutline,
		PencilOutlineIcon,
		NcButton,
	},

	props: {
		id: {
			type: Number,
			required: true,
		},
		uri: {
			type: String,
			required: true,
		},
		event: {
			type: String,
			required: true,
		},
		method: {
			type: String,
			required: true,
		},
	},

}

</script>

<style scoped lang="scss">
.webhook__code {
	display: inline-block;
	overflow-x: scroll;
	padding-block: var(--default-grid-baseline);
	text-wrap: nowrap;
	vertical-align: middle;
	width: 100%;
}

.update-delete {
	display: flex;
	gap: 5px;
	margin: 5px;
}
</style>
