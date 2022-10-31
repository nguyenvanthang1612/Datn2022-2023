<?php

namespace Magenest\Directory\ViewModel;

use Magenest\Directory\Helper\Data;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Customer\ViewModel\Address;
use Magento\Directory\Helper\Data as DataHelper;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerAddressViewModel extends Address
{
    /**
     * @var Data
     */
    private $helperDirectory;

    /**
     * @param DataHelper $helperData
     * @param AddressHelper $helperAddress
     * @param Data $helperDirectory
     */
    public function __construct(
        DataHelper $helperData,
        AddressHelper $helperAddress,
        Data $helperDirectory
    ) {
        parent::__construct($helperData, $helperAddress);
        $this->helperDirectory = $helperDirectory;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDataJson()
    {
        return $this->helperDirectory->getDataJson();
    }
}
