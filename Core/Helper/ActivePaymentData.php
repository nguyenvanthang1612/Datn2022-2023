<?php

namespace Magenest\Core\Helper;

use Magento\Framework\View\LayoutFactory;

class ActivePaymentData extends \Magento\Payment\Helper\Data
{
    /**
     * @var \Magento\Payment\Model\PaymentMethodList
     */
    protected $paymentMethodList;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        LayoutFactory $layoutFactory,
        \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\Config\Initial $initialConfig,
        \Magento\Payment\Model\PaymentMethodList $paymentMethodList
    ) {
        parent::__construct($context, $layoutFactory, $paymentMethodFactory, $appEmulation, $paymentConfig, $initialConfig);
        $this->paymentMethodList = $paymentMethodList;
    }

    public function getActivePaymentMethodList($sorted = true, $asLabelValue = false, $withGroups = false, $store = null)
    {
        $methods = [];
        $groups = [];
        $groupRelations = [];
        $activeMethod = $this->getActiveMethods($this->getPaymentMethods(), $store);
        foreach ($activeMethod as $code => $data) {
            $storeId = $store ? (int)$store->getId() : null;
            $storedTitle = $this->getMethodStoreTitle($code, $storeId);
            if (!empty($storedTitle)) {
                $methods[$code] = $storedTitle;
            }

            if ($asLabelValue && $withGroups && isset($data['group'])) {
                $groupRelations[$code] = $data['group'];
            }
        }
        if ($asLabelValue && $withGroups) {
            $groups = $this->_paymentConfig->getGroups();
            foreach ($groups as $code => $title) {
                $methods[$code] = $title;
            }
        }
        if ($sorted) {
            asort($methods);
        }
        if ($asLabelValue) {
            $labelValues = [];
            foreach ($methods as $code => $title) {
                $labelValues[$code] = [];
            }
            foreach ($methods as $code => $title) {
                if (isset($groups[$code])) {
                    $labelValues[$code]['label'] = $title;
                    if (!isset($labelValues[$code]['value'])) {
                        $labelValues[$code]['value'] = null;
                    }
                } elseif (isset($groupRelations[$code])) {
                    unset($labelValues[$code]);
                    $labelValues[$groupRelations[$code]]['value'][$code] = ['value' => $code, 'label' => $title];
                } else {
                    $labelValues[$code] = ['value' => $code, 'label' => $title];
                }
            }
            return $labelValues;
        }

        return $methods;
    }

    private function getMethodStoreTitle(string $code, ?int $storeId = null): string
    {
        $configPath = sprintf('%s/%s/title', self::XML_PATH_PAYMENT_METHODS, $code);
        return (string)$this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $methods
     * @param $store
     * @return array
     */
    private function getActiveMethods($allMethods, $store)
    {
        $activeMethods = [];
        $storeId = $store ? (int)$store->getId() : 0;
        foreach ($this->paymentMethodList->getActiveList($storeId) as $method) {
            $activeMethods[$method->getCode()] = $allMethods[$method->getCode()];
        }

        return $activeMethods;
    }
}