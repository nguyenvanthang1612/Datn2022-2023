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

namespace Magenest\Ahamove\Model;

use Magenest\Core\Model\ApiRequestBuilder;

/**
 * Class ApiConnect
 *
 * @package Magenest\Ahamove\Model
 */
class ApiConnect extends \Magenest\Core\Model\ApiConnect
{
    /**
     * @var bool
     */
    protected $useJson = true;

    /**
     * ApiConnect constructor.
     *
     * @param \Magenest\Ahamove\Helper\ShipmentHelper $helper
     * @param ApiRequestBuilder                       $requestBuilder
     * @param \Magento\Customer\Model\Session         $customerSession
     */
    public function __construct(
        \Magenest\Ahamove\Helper\ShipmentHelper $helper,
        ApiRequestBuilder $requestBuilder,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->requestUri = $helper->getUrlApi() ?: null;
        parent::__construct($requestBuilder, $customerSession);
    }

    public function estimatedFee($payload)
    {
        return $this->apiPost($this->getUri('v2/order/estimated_fee'), $payload);
    }

    public function serviceTypes($payload)
    {
        return $this->apiGet($this->getUri('v1/order/service_types'), $payload);
    }

    public function cancelOrder($payload)
    {
        return $this->apiPost($this->getUri('v1/order/cancel'), $payload);
    }

    public function createOrder($payload)
    {
        $this->useJson = false;

        return $this->apiPost($this->getUri('v1/order/create'), $payload);
    }

    public function sharedLink($payload)
    {
        $this->useJson = false;

        return $this->apiPost($this->getUri('v1/order/shared_link'), $payload);
    }

    protected function getHeader()
    {
        if ($this->useJson) {
            return parent::getHeader();
        }

        return [
            'Accept' => '*/*'
        ];
    }
}
