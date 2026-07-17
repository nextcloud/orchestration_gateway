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
		if (!$this->apiService->checkSchemaUrl($url)) {
			return new DataResponse('Invalid schema URL', Http::STATUS_BAD_REQUEST);
		}
		try {
			try {
				$params = $this->eventService->getSchema($event);
			} catch (\Throwable $e) {
				return new DataResponse('Failed to get the event schema', Http::STATUS_BAD_REQUEST);
			}
			$response = $this->apiService->request($url, params: $params, method: 'POST', isJson: true);
		} catch (Exception $e) {
			return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
		}
		if (isset($response['error'])) {
			return new DataResponse('Sending schema request unsuccessful: ' . $response['error'], Http::STATUS_BAD_REQUEST);
		}
		return new DataResponse([]);
	}
}
