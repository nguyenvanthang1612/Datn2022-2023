<?php

namespace Magenest\StoreLocator\Helper;

class Data extends \Magenest\StoreLocator\Helper\CoreData
{
    protected $scopeConfig;

    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /* Get system store config */
    public function getStoreConfig($node, $storeId = null)
    {
        if ($storeId != null) {
            return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }
        return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }
}
