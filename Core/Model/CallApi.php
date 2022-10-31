<?php

namespace Magenest\Core\Model;

/**
 * Class CallApi
 *
 * @package Magenest\Core\Model
 */
class CallApi {
	/**
	 * @var \Magento\Framework\HTTP\ZendClientFactory
	 */
	protected $httpClientFactory;

	public function __construct( \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory ) {
		$this->httpClientFactory = $httpClientFactory;
	}

	/**
	 * @param $method
	 * @param $params
	 * @param $uri
	 *
	 * @return string
	 * @throws \Zend_Http_Client_Exception
	 */
	public function ApiCall( $method, $params, $uri ) {
		$client = $this->httpClientFactory->create();
		$client->setUri( $uri );
		$client->setMethod( $method );
		$client->setHeaders( \Zend_Http_Client::CONTENT_TYPE, 'application/json' );
		$client->setHeaders( 'Accept', 'application/json' );
		if ( isset( $params['token'] ) ) {
			$client->setHeaders( 'Token', $params['token'] );
		}
		if ( $method == 'GET' ) {
			$client->setParameterGet( $params );
		} elseif ( $method == 'POST' ) {
			$client->setParameterPost( $params );
		} else {
			$client->setRawData( $params );
		}

		return $client->request()->getBody();
	}
}
