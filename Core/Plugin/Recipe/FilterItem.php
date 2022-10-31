<?php


namespace Magenest\Core\Plugin\Recipe;


class FilterItem
{
    const DEFAULT_VN_STORE = 'vn';
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Maijindou\Recipe\Model\Recipe\FilterItem $subject
     * @param callable $proceed
     * @return array|mixed|string|null
     */
    public function aroundGetName(\Maijindou\Recipe\Model\Recipe\FilterItem $subject, callable $proceed)
    {
        try {
            $storeCode = $this->storeManager->getStore()->getCode();
        } catch (\Throwable $e) {
            $storeCode = self::DEFAULT_VN_STORE;
        }

        if (empty($subject->getAttributeName())) {
            return $storeCode == self::DEFAULT_VN_STORE ? $subject->getComponentLocalName() : $subject->getComponentName();
        }

        return $storeCode == self::DEFAULT_VN_STORE ? $subject->getAttributeLocalName() : $subject->getAttributeName();
    }
}