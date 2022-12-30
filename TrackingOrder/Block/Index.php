<?php

namespace Magenest\TrackingOrder\Block;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
    * @var \Magenest\TrackingOrder\Helper\ConfigData
    */
    protected $ksHelperData;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $ksContext
     * @param \Magenest\TrackingOrder\Helper\ConfigData $ksHelperData
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $ksContext,
        \Magenest\TrackingOrder\Helper\ConfigData $ksHelperData,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->ksHelperData = $ksHelperData;
        $this->customerSession = $customerSession;
        parent::__construct($ksContext);
    }

    /**
     * Button Color
     * @return String
     */
    public function getButtonColor()
    {
        return $this->ksHelperData->getCompanyConfig('button_color');
    }

    /**
     * Button Text
     * @return String
     */
    public function getButtonText()
    {
        return $this->ksHelperData->getCompanyConfig('button_text');
    }

    /**
     * Module Status
     * @return Int
     */
    public function getModuleStatus()
    {
        return $this->ksHelperData->getCompanyConfig('enable');
    }

    /**
     * Button Text Color
     * @return String
     */
    public function getButtonTextColor()
    {
        return  $this->ksHelperData->getCompanyConfig('button_text_color');
    }

    /**
     * @return bool
     */
    public function checkCustomerLogin()
    {
        if ($this->customerSession->isLoggedIn()) {
            return 'hide';
        }
    }
}
