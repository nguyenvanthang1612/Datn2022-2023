<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Project extension
 * NOTICE OF LICENSE
 *
 * @author   PhongNguyen
 * @category Magenest
 * @package  Magenest_Project
 */

namespace Magenest\Ahamove\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory;

/**
 * Class Source
 *
 * @package Magenest\Ahamove\Model\Config\Source
 */
class Source implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Source constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->collectionFactory->create()->toOptionArray();
            array_unshift($this->options, ['label' => __('Please select Source'), 'value' => null]);
        }
        return $this->options;
    }
}
