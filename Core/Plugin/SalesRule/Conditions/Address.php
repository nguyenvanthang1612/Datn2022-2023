<?php


namespace Magenest\Core\Plugin\SalesRule\Conditions;


class Address
{
    /**
     * @var \Magenest\Core\Helper\ActivePaymentData
     */
    protected $_activePayment;
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_directoryCountry;
    /**
     * @var \Magento\Directory\Model\Config\Source\Allregion
     */
    protected $_directoryAllregion;
    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    protected $_shippingAllmethods;

    public function __construct(
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magenest\Core\Helper\ActivePaymentData $activePaymentData
    ) {
        $this->_directoryCountry = $directoryCountry;
        $this->_directoryAllregion = $directoryAllregion;
        $this->_shippingAllmethods = $shippingAllmethods;
        $this->_activePayment = $activePaymentData;
    }


    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $subject
     * @param callable $proceed
     * @return array|mixed|null
     */
    public function aroundGetValueSelectOptions(\Magento\SalesRule\Model\Rule\Condition\Address $subject, callable $proceed)
    {
        if (!$subject->hasData('value_select_options')) {
            switch ($subject->getAttribute()) {
                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray();
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray();
                    break;

                case 'shipping_method':
                    $options = $this->_shippingAllmethods->toOptionArray(true);
                    break;

                case 'payment_method':
                    $options = $this->_activePayment->getActivePaymentMethodList(true, true, true);
                    break;

                default:
                    $options = [];
            }
            $subject->setData('value_select_options', $options);
        }
        return $subject->getData('value_select_options');
    }
}