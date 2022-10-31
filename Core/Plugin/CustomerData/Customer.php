<?php

namespace Magenest\Core\Plugin\CustomerData;

use Magento\Customer\Helper\Session\CurrentCustomer;

class Customer
{
    protected $currentCustomer;

    public function __construct(
        CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        if (!empty($result)) {
            $customer = $this->currentCustomer->getCustomer();
            $result['lastname'] = $customer->getLastname();
        }
        return $result;
    }
}