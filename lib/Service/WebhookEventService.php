<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OrchestrationGateway\Service;

use OCP\IL10N;

class WebhookEventService {

	public function __construct(
		private IL10N $l10n,
	) {
	}
	/**
	 * List all events that can be registered as a webhook
	 *
	 * @return array
	 *
	 */
	public function listEvents(): array {

		$events = [];
		if (class_exists('OCA\\Forms\\Events\\FormSubmittedEvent')) {
			$events[] = [
				'name' => 'FormSubmittedEvent',
				'description' => $this->l10n->t('A submission to a form in Nextcloud Forms'),
				'path' => "OCA\Forms\Events\FormSubmittedEvent",
			];
		}

		if (class_exists('OCA\\Tables\\Event\\RowAddedEvent')) {
			$events[] = [
				'name' => 'RowAddedEvent',
				'description' => $this->l10n->t('A row has been added to a table in Nextcloud Tables'),
				'path' => "OCA\Tables\Event\RowAddedEvent",
			];
		}

		if (class_exists('OCA\\Tables\\Event\\RowDeletedEvent')) {
			$events[] = [
				'name' => 'RowDeletedEvent',
				'description' => $this->l10n->t('A row has been deleted from a table in Nextcloud Tables'),
				'path' => "OCA\Tables\Event\RowDeletedEvent",
			];
		}

		if (class_exists('OCA\\Tables\\Event\\RowUpdatedEvent')) {
			$events[] = [
				'name' => 'RowUpdatedEvent',
				'description' => $this->l10n->t('A row has been updated in a table in Nextcloud Tables'),
				'path' => "OCA\Tables\Event\RowUpdatedEvent",
			];
		}

		if (class_exists('OCP\\Calendar\\Events\\CalendarObjectCreatedEvent')) {
			$events[] = [
				'name' => 'CalendarObjectCreatedEvent',
				'description' => $this->l10n->t('A new object has been created in a Nextcloud calendar'),
				'path' => "OCP\Calendar\Events\CalendarObjectCreatedEvent",
			];
		}

		if (class_exists('OCP\\Calendar\\Events\\CalendarObjectMovedEvent')) {
			$events[] = [
				'name' => 'CalendarObjectMovedEvent',
				'description' => $this->l10n->t('An object has been moved from a Nextcloud calendar to another'),
				'path' => "OCP\Calendar\Events\CalendarObjectMovedEvent",
			];
		}

		if (class_exists('OCP\\Calendar\\Events\\CalendarObjectMovedToTrashEvent')) {
			$events[] = [
				'name' => 'CalendarObjectMovedToTrashEvent',
				'description' => $this->l10n->t('An object has been moved to the trash in a Nextcloud calendar'),
				'path' => "OCP\Calendar\Events\CalendarObjectMovedToTrashEvent",
			];
		}

		if (class_exists('OCP\\Calendar\\Events\\CalendarObjectRestoredEvent')) {
			$events[] = [
				'name' => 'CalendarObjectRestoredEvent',
				'description' => $this->l10n->t('An object has been restored from trash in a Nextcloud calendar'),
				'path' => "OCP\Calendar\Events\CalendarObjectRestoredEvent",
			];
		}

		if (class_exists('OCP\\Calendar\\Events\\CalendarObjectUpdatedEvent')) {
			$events[] = [
				'name' => 'CalendarObjectUpdatedEvent',
				'description' => $this->l10n->t('An object has been changed in a Nextcloud calendar'),
				'path' => "OCP\Calendar\Events\CalendarObjectUpdatedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeCreatedEvent')) {
			$events[] = [
				'name' => 'BeforeNodeCreatedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be created'),
				'path' => "OCP\Files\Events\Node\BeforeNodeCreatedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeTouchedEvent')) {
			$events[] = [
				'name' => 'BeforeNodeTouchedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be changed'),
				'path' => "OCP\Files\Events\Node\BeforeNodeTouchedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeWrittenEvent')) {
			$events[] = [
				'name' => 'BeforeNodeWrittenEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be written'),
				'path' => "OCP\Files\Events\Node\BeforeNodeWrittenEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeReadEvent')) {
			$events[] = [
				'name' => 'BeforeNodeReadEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be read'),
				'path' => "OCP\Files\Events\Node\BeforeNodeReadEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeDeletedEvent')) {
			$events[] = [
				'name' => 'BeforeNodeDeletedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be deleted'),
				'path' => "OCP\Files\Events\Node\BeforeNodeDeletedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeCopiedEvent')) {
			$events[] = [
				'name' => 'BeforeNodeCopiedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be copied'),
				'path' => "OCP\Files\Events\Node\BeforeNodeCopiedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeRestoredEvent')) {
			$events[] = [
				'name' => 'BeforeNodeRestoredEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be restored'),
				'path' => "OCP\Files\Events\Node\BeforeNodeRestoredEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\BeforeNodeRenamedEvent')) {
			$events[] = [
				'name' => 'BeforeNodeRenamedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) will be renamed'),
				'path' => "OCP\Files\Events\Node\BeforeNodeRenamedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeCreatedEvent')) {
			$events[] = [
				'name' => 'NodeCreatedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been created'),
				'path' => "OCP\Files\Events\Node\NodeCreatedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeTouchedEvent')) {
			$events[] = [
				'name' => 'NodeTouchedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been changed'),
				'path' => "OCP\Files\Events\Node\NodeTouchedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeWrittenEvent')) {
			$events[] = [
				'name' => 'NodeWrittenEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been written'),
				'path' => "OCP\Files\Events\Node\NodeWrittenEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeDeletedEvent')) {
			$events[] = [
				'name' => 'NodeDeletedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been deleted'),
				'path' => "OCP\Files\Events\Node\NodeDeletedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeCopiedEvent')) {
			$events[] = [
				'name' => 'NodeCopiedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been copied'),
				'path' => "OCP\Files\Events\Node\NodeCopiedEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeRestoredEvent')) {
			$events[] = [
				'name' => 'NodeRestoredEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been restored'),
				'path' => "OCP\Files\Events\Node\NodeRestoredEvent",
			];
		}

		if (class_exists('OCP\\Files\\Events\\Node\\NodeRenamedEvent')) {
			$events[] = [
				'name' => 'NodeRenamedEvent',
				'description' => $this->l10n->t('A node in Nextcloud (a file/folder/similar) has been renamed'),
				'path' => "OCP\Files\Events\Node\NodeRenamedEvent",
			];
		}

		if (class_exists('OCP\\SystemTag\\TagAssignedEvent')) {
			$events[] = [
				'name' => 'TagAssignedEvent',
				'description' => $this->l10n->t('A tag has been added to an object in Nextcloud'),
				'path' => "OCP\SystemTag\TagAssignedEvent",
			];
		}

		if (class_exists('OCP\\SystemTag\\TagUnassignedEvent')) {
			$events[] = [
				'name' => 'TagUnassignedEvent',
				'description' => $this->l10n->t('A tag has been removed from an object in Nextcloud'),
				'path' => "OCP\SystemTag\TagUnassignedEvent",
			];
		}

		if (class_exists('OCA\\Mail\\Events\\MessageSentEvent')) {
			$events[] = [
				'name' => 'MessageSentEvent',
				'description' => $this->l10n->t('A mail has been sent'),
				'path' => "OCA\Mail\Events\MessageSentEvent",
			];
		}

		if (class_exists('OCA\\Mail\\Events\\MessageDeletedEvent')) {
			$events[] = [
				'name' => 'MessageDeletedEvent',
				'description' => $this->l10n->t('A mail has been deleted'),
				'path' => "OCA\Mail\Events\MessageDeletedEvent",
			];
		}

		if (class_exists('OCA\\Mail\\Events\\MessageFlaggedEvent')) {
			$events[] = [
				'name' => 'MessageFlaggedEvent',
				'description' => $this->l10n->t('A mail has been flagged'),
				'path' => "OCA\Mail\Events\MessageFlaggedEvent",
			];
		}

		if (class_exists('OCA\\Mail\\Events\\NewMessageReceivedEvent')) {
			$events[] = [
				'name' => 'NewMessageReceivedEvent',
				'description' => $this->l10n->t('A new mail has been received'),
				'path' => "OCA\Mail\Events\NewMessageReceivedEvent",
			];
		}

		return $events;
	}
}
