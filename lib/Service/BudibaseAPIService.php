<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OrchestrationGateway\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use OCP\Config\IUserConfig;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\Notification\IManager;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Service to make requests to Budibase API
 */
class BudibaseAPIService {

	private IClient $client;

	public function __construct(
		private LoggerInterface $logger,
		private IL10N $l10n,
		private IUserConfig $userConfig,
		private IURLGenerator $urlGenerator,
		private IUserManager $userManager,
		private IManager $notificationManager,
		IClientService $clientService,
	) {
		$this->client = $clientService->newClient();
	}


	/**
	 * Make an authenticated HTTP request to Budibase
	 * @param array $params Query parameters (key/val pairs)
	 * @param string $method HTTP query method
	 * @return array decoded request result or error
	 */
	public function request(string $url, array $params = [], string $method = 'GET'): array {
		try {

			if (count($params) > 0) {
				$options['body'] = $params;
			}

			if ($method === 'GET') {
				$response = $this->client->get($url, $options);
			} elseif ($method === 'POST') {
				error_log($url);
				$response = $this->client->post($url, $options);
			} elseif ($method === 'PUT') {
				$response = $this->client->put($url, $options);
			} elseif ($method === 'DELETE') {
				$response = $this->client->delete($url, $options);
			} else {
				return ['error' => $this->l10n->t('Bad HTTP method')];
			}
			$body = $response->getBody();
			$respCode = $response->getStatusCode();

			if ($respCode >= 400) {
				return ['error' => $this->l10n->t('Bad credentials')];
			} else {
				return json_decode($body, true) ?: [];
			}
		} catch (ClientException|ServerException $e) {
			$responseBody = $e->getResponse()->getBody();
			$parsedResponseBody = json_decode($responseBody, true);
			if ($e->getResponse()->getStatusCode() === 404) {
				// Only log inaccessible Budibase links as debug
				$this->logger->debug('Budibase API client or server error', ['response_body' => $responseBody, 'exception' => $e]);
			} else {
				$this->logger->warning('Budibase API client or server error', ['response_body' => $responseBody, 'exception' => $e]);
			}
			return [
				'error' => $e->getMessage(),
				'body' => $parsedResponseBody,
			];
		} catch (Exception|Throwable $e) {
			$this->logger->warning('Budibase API request error', ['exception' => $e]);
			return ['error' => $e->getMessage()];
		}
	}
}
