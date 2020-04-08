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

	public function register($webinarId, array $args = []) {
		return $this->post( "webinars/{$webinarId}/registrants", $args );
	}

	public function getDetails($webinarId, array $query = []) {
		return $this->get("webinars/{$webinarId}", $query);
	}

	public function getQuestions($webinarId) {
		return $this->get("webinars/{$webinarId}/registrants/questions");
	}
}
