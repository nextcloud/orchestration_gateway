<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OrchestrationGateway\Settings;

use OCA\OrchestrationGateway\AppInfo\Application;
use OCA\OrchestrationGateway\Service\WebhookEventService;
use OCA\WebhookListeners\Db\WebhookListenerMapper;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IDelegatedSettings;

class Admin implements IDelegatedSettings {

	public function __construct(
		private IInitialState $initialStateService,
		private WebhookEventService $eventService,
		private WebhookListenerMapper $mapper,
		private IURLGenerator $urlGenerator,
		private IL10N $l10n,
	) {
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$webhookListeners = $this->mapper->getAll();
		$events = array_column($this->eventService->listEvents(), 'path');

		$this->initialStateService->provideInitialState('webhook-listeners', $webhookListeners);
		$this->initialStateService->provideInitialState('webhook-events', $events);
		$this->initialStateService->provideInitialState('webhooksDocUrl', $this->urlGenerator->linkToDocs('webhook_listeners'));
		return new TemplateResponse(Application::APP_ID, 'adminSettings');
	}

	public function getSection(): string {
		return Application::APP_ID;
	}

	public function getPriority(): int {
		return 10;
	}

	public function getName(): string {
		return $this->l10n->t('Orchestration gateway');
	}

	public function getAuthorizedAppConfig(): array {
		return [];
	}
}
