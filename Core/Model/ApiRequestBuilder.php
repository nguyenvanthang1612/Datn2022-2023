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

use Magenest\Core\Model\Http\Client\CurlFactory;
use Magenest\Core\Model\Http\Client\Curl;

/**
 * Class ApiRequestBuilder
 *
 * @package Magenest\Core\Model
 */
class ApiRequestBuilder
{
    const DEFAULT_CURL_TIMEOUT = 30000;
    const HTTP_GET_METHOD      = 'GET';
    const HTTP_POST_METHOD     = 'POST';
    const HTTP_PUT_METHOD      = 'PUT';

    protected $_helper;

    protected $_curlFactory;

    /**
     * @var Curl|null
     */
    protected $_curlRequest = null;

    /**
     * @var \Magento\Framework\Url\QueryParamsResolverInterface
     */
    protected $queryParamResolver;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * ApiRequestBuilder constructor.
     *
     * @param \Magenest\Ahamove\Helper\ShipmentHelper             $helper
     * @param CurlFactory                                         $curlFactory
     * @param \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver
     * @param \Magento\Framework\Session\SessionManagerInterface  $session
     */
    public function __construct(
        \Magenest\Ahamove\Helper\ShipmentHelper $helper,
        CurlFactory $curlFactory,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver,
        \Magento\Framework\Session\SessionManagerInterface $session
    ) {
        $this->_session           = $session;
        $this->queryParamResolver = $queryParamsResolver;
        $this->_helper            = $helper;
        $this->_curlFactory       = $curlFactory;
    }

    public function initCurlRequest($headers = [])
    {
        if ($this->_curlRequest !== null) {
            $this->_curlRequest = null;
        }
        $this->_curlRequest = $this->_curlFactory->create();
        if (!empty($headers) && is_array($headers)) {
            foreach ($headers as $key => $header) {
                $this->_curlRequest->addHeader($key, $header);
            }
        }

        return $this->_curlRequest;
    }

    public function makeCurlRequest($method, $uri, $params = [], $debugOn = null)
    {
        $this->_curlRequest->setTimeout(60);
        if ($debugOn) {
            $this->_helper->debug($method . ":" . $uri);
            $this->_helper->debug(var_export($params, true));
        }
        try {
            switch ($method) {
                case self::HTTP_GET_METHOD:
                {
                    if (!empty($params)) {
                        $uri .= $this->processQueryParams($params);
                    }
                    $this->_curlRequest->get($uri);
                    break;
                }
                case self::HTTP_POST_METHOD:
                {
                    $this->_curlRequest->post($uri, $params);
                    break;
                }
                case self::HTTP_PUT_METHOD:
                {
                    $params = $this->_helper->serialize($params);
                    $this->_curlRequest->put($uri, $params);
                    break;
                }
                default:
                {
                    throw new \Exception(__("Method is not defined"));
                }
            }
        } catch (\Exception $e) {
            $this->_helper->debug($e);
        }
        if ($debugOn) {
            $this->_helper->debug(var_export($this->getCurlRespStatus(), true));
            $this->_helper->debug(var_export($this->getCurlRawBody(), true));
        }
    }

    protected function processQueryParams($params)
    {
        $paramData = [];
        foreach ($params as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $paramData[$key] = $value;
        }

        return $this->buildQueryParams($paramData);
    }

    private function buildQueryParams($params)
    {
        $this->queryParamResolver->unsetData();
        $this->queryParamResolver->addQueryParams($params);
        $result = '?' . $this->queryParamResolver->getQuery();
        $this->queryParamResolver->unsetData();

        return $result;
    }

    protected function getCurlRespStatus()
    {
        return $this->_curlRequest->getStatus();
    }

    protected function getCurlRawBody()
    {
        return $this->_curlRequest->getBody();
    }

    public function getResponseBody()
    {
        if ($this->_curlRequest == null) {
            return '';
        }
        $responseBody = $this->getCurlRawBody();

        return $this->prepareResponseData($responseBody);
    }

    private function prepareResponseData($response)
    {
        return $this->_helper->unserialize($response);
    }
}
