<?php

namespace Zoom\Endpoint;

use Zoom\Interfaces\Request;

/**
 * Class Users
 * @package Zoom\Interfaces
 */
class Recording extends Request {

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
    return $this->get("users/{$userId}/recordings/", $query);
  }

}
