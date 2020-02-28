<?php

/**
 * @copyright  https://github.com/UsabilityDynamics/zoom-api-php-client/blob/master/LICENSE
 */
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
  public function list( array $query = [] ) {
    return $this->get( "webinars", $query );
  }

}
