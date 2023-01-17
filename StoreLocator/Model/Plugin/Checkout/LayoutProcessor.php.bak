<?php


namespace Magenest\StoreLocator\Model\Plugin\Checkout;


use Magenest\StoreLocator\Helper\StoreData;

class LayoutProcessor
{
    protected $storeData;

    public function __construct(
        StoreData $storeData
    ) {
        $this->storeData = $storeData;
    }

    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, $jsLayout)
    {
        $opt_val = [];
        $allOptions = [];
        $customAttributeCode = 'store_name_list';
        $storeData = $this->storeData->getStoreData();
        foreach ($storeData as $key => $data) {
            $opt_val['value'] = $data->getData('id');
            $opt_val['label'] = $data->getData('name');
            $allOptions[] = $opt_val;
            $customField = [
                'component' => 'Magento_Ui/js/form/element/select',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select',
                    'id' => 'store_name_list',
                ],
                'dataScope' => 'shippingAddress.custom_attributes.store_name_list',
                'label' => 'Store',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => ['required-entry' => false],
                'sortOrder' => 150,
                'id' => 'store_name_list',
                'options' => $allOptions
            ];
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$customAttributeCode] = $customField;

        return $jsLayout;
    }
}
