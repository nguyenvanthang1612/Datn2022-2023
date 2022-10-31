<?php

namespace Magenest\Ahamove\Model\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class CancelReason extends AbstractSource
{
    const CANCEL_REASON_OPTIONS_PATH = 'order_management/cancel_reason/cancel_reason_options';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Json
     */
    protected $json;

    /**
     * CancelReason constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     */
    public function __construct(ScopeConfigInterface $scopeConfig, Json $json)
    {
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        $reasonOptions = $this->scopeConfig->getValue(
            self::CANCEL_REASON_OPTIONS_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!empty($reasonOptions)) {
            foreach ($this->json->unserialize($reasonOptions) as $key => $value){
                $options[$key] = __($value['cancel_reason_option']);
            }
        }
        $options['other_reason'] = __("Other Reason.");
        return $options;
    }
}
