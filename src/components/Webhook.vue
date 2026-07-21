<!--
  - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<tr>
		<td>{{ id }}</td>
		<td>
			<code class="webhook__code">{{ event }}</code>
		</td>
		<td>
			{{ method }}
		</td>
		<td class="webhook__scrollable">
			{{ uri }}
		</td>
		<td>
			<code class="webhook__code">{{ eventFilter }}</code>
		</td>
		<td>
			{{ userIdFilter }}
		</td>
		<td>
			<code class="webhook__code">{{ tokenNeeded }}</code>
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
		event: {
			type: String,
			required: true,
		},
		method: {
			type: String,
			required: true,
		},
		uri: {
			type: String,
			required: true,
		},
		eventFilter: {
			type: Array,
			required: true,
		},
		userIdFilter: {
			type: String,
			required: true,
		},
		tokenNeeded: {
			type: Array,
			required: true,
		},
	},

}

</script>

<style scoped lang="scss">

td {
	padding: 5px 10px 5px 10px;
	max-width: 800px;
}

.webhook__code {
	display: inline-block;
	overflow-x: scroll;
	padding-block: var(--default-grid-baseline);
	text-wrap: nowrap;
	vertical-align: middle;
	width: 100%;
}

.webhook__scrollable {
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
