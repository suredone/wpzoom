<?php

namespace Zoom\Endpoint;

use Zoom\Interfaces\Request;

/**
 * Class Users
 * @package Zoom\Interfaces
 */
class Webinar extends Request {

	/**
	 * Webinar constructor.
	 * @param $apiKey
	 * @param $apiSecret
	 */
	public function __construct($apiKey, $apiSecret) {
		parent::__construct($apiKey, $apiSecret);
	}

	/**
	 * List
	 *
	 * @param array $query
	 * @return array|mixed
	 */
	public function list(string $userId, array $query = []) {
		return $this->get("users/{$userId}/webinars", $query);
	}

	public function register(int $webinarId, array $args = []) {
		return $this->post( "webinars/{$webinarId}/registrants", $args );
	}

}
