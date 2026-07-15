<?php

declare(strict_types=1);

namespace OCA\OrchestrationGateway\Controller;

use Exception;
use OCA\OrchestrationGateway\Service\BudibaseAPIService;
use OCA\OrchestrationGateway\Service\WebhookEventService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class WebhooksController extends Controller {

	public function __construct(
		string $AppName,
		IRequest $request,
		private BudibaseAPIService $apiService,
		private WebhookEventService $eventService,
	) {
		parent::__construct($AppName, $request);
	}

	/**
	 * Send an example schema request to Budibase
	 *
	 * @param string $url
	 * @return DataResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/send-schema')]
	public function sendSchema(string $url, string $event): DataResponse {
		try {
			$response = $this->apiService->request($url, params: $this->eventService->getSchema($event), method: 'POST');
		} catch (Exception $e) {
			return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
		}
		if (!$response['ok']) {
			return new DataResponse('Sending schema request unsuccessful', Http::STATUS_BAD_REQUEST);
		}
		return new DataResponse([]);
	}
}
