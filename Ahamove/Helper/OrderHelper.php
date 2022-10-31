<?php

namespace Magenest\Ahamove\Helper;

use Magenest\Ahamove\Model\Source\CancelReason;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class OrderHelper extends Helper
{
    /**
     * @var CancelReason
     */
    protected $cancelReason;

    /**
     * OrderHelper constructor.
     * @param \Monolog\Logger $logger
     * @param Json $serializer
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param Context $context
     * @param CancelReason $cancelReason
     */
    public function __construct(
        \Monolog\Logger $logger,
        Json $serializer,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Context $context,
        CancelReason $cancelReason
    ) {
        $this->cancelReason = $cancelReason;
        parent::__construct($logger, $serializer, $storeManager, $registry, $context);
    }

    public function getCancelReason(\Magento\Framework\App\RequestInterface $request, $isCreditmemo = false)
    {
        $result = '';
        if ($isCreditmemo) {
            $params = $request->getParam('creditmemo');
        } else {
            $params = $request->getParams();
        }
        if ($reason = isset($params['cancel_reason']) ? $params['cancel_reason'] : false) {
            $result .= $this->cancelReason->getOptionText($reason);
        }
        if (isset($params['other_reason']) && !empty($params['other_reason'])) {
            $result .= " " . $params['other_reason'];
        }

        return $result;
    }

    public function isManuallyProcess()
    {
        return (bool)$this->getStoreConfig(self::KEY_MANUAL_PROCESS_ORDER, ScopeInterface::SCOPE_WEBSITES);
    }
}
