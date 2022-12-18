<?php

namespace Magenest\SplitOrder\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class AttributeQty implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'qty' => __('Stock Quantity (Inventory value)'),
            'status' => __('Stock Status (In or Out of stock)')
        ];
    }
}
