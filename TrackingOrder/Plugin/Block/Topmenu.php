<?php

namespace Magenest\TrackingOrder\Plugin\Block;

use Magento\Framework\Data\Tree\NodeFactory;

class Topmenu
{
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $ksStoreManager;

    /**
     * @var Magento\Framework\Data\Tree\NodeFactory
     */
    protected $ksNodeFactory;

    /**
     * @var Magenest\TrackingOrder\Helper\ConfigData
     */
    protected $ksHelperData;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $ksStoreManager
     * @param \Magento\Framework\Data\Tree\NodeFactory $ksNodeFactory
     * @param \Magenest\TrackingOrder\Helper\ConfigData $ksHelperData
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $ksStoreManager,
        NodeFactory $ksNodeFactory,
        \Magenest\TrackingOrder\Helper\ConfigData $ksHelperData
    ) {
        $this->ksStoreManager = $ksStoreManager;
        $this->ksNodeFactory = $ksNodeFactory;
        $this->ksHelperData = $ksHelperData;
    }

    /**
     * return menu
     *
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $ksSubject,
        $ksOutermostClass = '',
        $ksChildrenWrapClass = '',
        $ksLimit = 0
    ) {
        $ksNode = $this->ksNodeFactory->create(
            [
                'data' => $this->getNodeAsArray(),
                'idField' => 'id',
                'tree' => $ksSubject->getMenu()->getTree()
            ]
        );
        $ksSubject->getMenu()->addChild($ksNode);
    }

    /**
    * return menu label
    *
    */
    protected function getNodeAsArray()
    {
        $ksEnableLink = $this->ksHelperData->getCompanyConfig('allow_topmenu');
        $ksEnableModule = $this->ksHelperData->getCompanyConfig('enable');
        if ($ksEnableLink == 1 && $ksEnableModule == 1) {
            $ksBaseUrl = $this->ksStoreManager->getStore()->getBaseUrl();
            return [
                'name' => __('Track Order'),
                'id' => 'track_order',
                'url' => ''.$ksBaseUrl.'trackingorder',
                'has_active' => false,
                'is_active' => false
            ];
        }
    }
}
