<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Project extension
 * NOTICE OF LICENSE
 *
 * @author   PhongNguyen
 * @category Magenest
 * @package  Magenest_Project
 */

namespace Magenest\Core\Model;

class ApiConnect
{
    const SUCCESS_CODE = 0;

    protected $apiRequestBuilder;

    protected $customerSession;

    protected $requestUri = null;

    protected $_bearerToken = null;

    protected $key = null;

    protected $time = '';

    /**
     * ApiConnect constructor.
     *
     * @param ApiRequestBuilder               $requestBuilder
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        ApiRequestBuilder $requestBuilder,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->apiRequestBuilder = $requestBuilder;
        $this->customerSession   = $customerSession;
        $this->init();
    }

    protected function setRequestUri($uri)
    {
        $this->requestUri = $uri;

        return $this;
    }

    protected function getRequestUri()
    {
        return $this->requestUri;
    }

    protected function init()
    {
        $this->time = time();
    }

    protected function getUri($action)
    {
        return $this->getRequestUri() . $action;
    }

    protected function apiPost($uri, $payload)
    {
        $this->apiRequestBuilder->initCurlRequest($this->getHeader());
        $this->apiRequestBuilder->makeCurlRequest(ApiRequestBuilder::HTTP_POST_METHOD, $uri, $payload, true);

        return $this->apiRequestBuilder->getResponseBody();
    }

    protected function apiGet($uri, $payload)
    {
        $this->apiRequestBuilder->initCurlRequest($this->getHeader());
        $this->apiRequestBuilder->makeCurlRequest(ApiRequestBuilder::HTTP_GET_METHOD, $uri, $payload, true);

        return $this->apiRequestBuilder->getResponseBody();
    }

    protected function setBearerToken($token)
    {
        $this->_bearerToken = $token;

        return $this;
    }

    protected function getBearerToken()
    {
        return $this->_bearerToken;
    }

    protected function getHeader()
    {
        $header = [
            'Accept'                        => 'application/json',
            \Zend_Http_Client::CONTENT_TYPE => 'application/json'
        ];

        return $header;
    }
}
