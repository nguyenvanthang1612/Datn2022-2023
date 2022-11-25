<?php

namespace Magenest\SplitOrder\Model\Adminhtml\System\Config\Source;

class SplitOrderAttributeOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Split According To Attribute')],
            ['value' => 1, 'label' => __('One')],
            ['value' => 2, 'label' => __('Two')],
        ];
    }
}
