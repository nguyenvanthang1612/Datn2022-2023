<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Rich's extension
 * NOTICE OF LICENSE
 *
 * @author TrangHa
 * @category Magenest
 * @package Magenest_Rich's
 * @Date 08/07/2021
 */

namespace Magenest\RegionStock\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddAddressData implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $source = $observer->getSource();
        $request = $observer->getRequest();
        $postValue = $request->getPostValue();
        $attributes = ['city', 'city_id', 'district', 'district_id', 'ward', 'ward_id'];
        foreach ($attributes as $attribute) {
            if (isset($postValue['general'][$attribute])) {
                if ($attribute == 'type' && $postValue['general'][$attribute]) {
                    $source->setData($attribute,  implode(",", $postValue['general']['type']));
                } else {
                    $source->setData($attribute, $postValue['general'][$attribute]);
                }
            }
        }
    }
}
