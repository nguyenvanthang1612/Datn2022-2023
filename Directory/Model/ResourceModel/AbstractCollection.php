<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\ResourceModel;

/**
 * Class City
 * @package Magenest\Directory\Model\ResourceModel
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Foreign key
     */
    protected $_foreignKey = null;

    /**
     * @var string
     */
    protected $_defaultOptionLabel = '';

    /**
     * @var string
     */
    protected $_defaultValue = '';

    /**
     * @var string
     */
    protected $_label = 'default_name';

    /**
     * @var bool
     */
    protected $_sortable = false;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = $this->prepareOptionArray();
        array_unshift($options, ['value' => '', 'label' => __($this->_defaultOptionLabel), $this->_foreignKey => $this->_defaultValue]);

        return $options;
    }

    /**
     * Prepare option array
     *
     * @return array
     */
    public function prepareOptionArray()
    {
        $options = [];
        if  (!empty($this->getItems())) {
            foreach ($this->getItems() as $item) {
                $data = [];
                foreach (
                    [
                        'value' => $this->getIdFieldName(),
                        'label' => $this->_label,
                        'name' => 'name',
                        'full_name' => 'default_name',
                        'disable_on_storefront' => 'disable_on_storefront',
                        $this->_foreignKey => $this->_foreignKey,
                    ]
                    as $code => $field) {
                    $data[$code] = $item->getData($field);
                }

                $options[] = $data;
            }

            if ($this->_sortable) {
                usort($options, function ($first, $second) {
                    return ($first['name'] <= $second['name']) ? -1 : 1;
                });
            }
        }
        return $options;
    }

    /**
     * Set label
     *
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    /**
     * Get foreign eky
     *
     * @return null|string
     */
    public function getForeignKey()
    {
        return $this->_foreignKey;
    }
}
