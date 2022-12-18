<?php

namespace Magenest\TrackingOrder\Block\Links;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $ksStoreManager;

    /**
    * @var \Magenest\TrackingOrder\Helper\ConfigData
    */
    protected $ksHelperData;

    /**
     * @param \Magento\Framework\App\Action\Context $ksContext
     * @param \Magento\Store\Model\StoreManagerInterface $ksStoreManager
     * @param \Magenest\TrackingOrder\Helper\ConfigData $ksHelperData
     * @param array $ksData = []
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $ksContext,
        \Magento\Store\Model\StoreManagerInterface $ksStoreManager,
        \Magenest\TrackingOrder\Helper\ConfigData $ksHelperData,
        array $ksData = []
    ) {
        $this->ksStoreManager = $ksStoreManager;
        $this->ksHelperData = $ksHelperData;
        parent::__construct($ksContext, $ksData);
    }

    /**
     *  return url of link
     * @return String
     */
    public function getHref()
    {
        $ksEnableLink = $this->ksHelperData->getCompanyConfig('allow_toplink');
        if ($ksEnableLink == 1) {
            $ksBaseUrl = $this->ksStoreManager->getStore()->getBaseUrl();
            $ksPageUrl ='' . $ksBaseUrl . 'trackingorder';
            return $this->getUrl($ksPageUrl);
        }
    }

    /**
     *  return label of link
     * @return  String
     */
    public function getLabel()
    {
        $ksEnableLink = $this->ksHelperData->getCompanyConfig('allow_toplink');
        if ($ksEnableLink == 1) {
            return 'Track Order';
        }
    }
}
