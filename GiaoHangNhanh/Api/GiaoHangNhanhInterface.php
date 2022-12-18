<?php
namespace Magenest\GiaoHangNhanh\Api;

interface GiaoHangNhanhInterface
{
    /**
     * @param int $cartId
     * @param string $street
     * @param string $region
     * @param string $city
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Zend_Http_Client_Exception
     */
    public function estimateShipping($cartId, $street, $region, $city);

}