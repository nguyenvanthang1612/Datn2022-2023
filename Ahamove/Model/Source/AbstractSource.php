<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_SS extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_SS
 * @noinspection DuplicatedCode
 */

namespace Magenest\Ahamove\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

abstract class AbstractSource implements OptionSourceInterface
{
    public function getOptionText($value)
    {
        $options = static::getAllOptions();
        foreach ($options as $key => $option) {
            if ($key == $value) {
                return $option;
            }
        }

        return "";
    }

    /**
     * @return array
     */
    abstract public function getAllOptions();

    public function toOptionArray()
    {
        $allOptions = $this->getAllOptions();
        $result = [];
        foreach ($allOptions as $value => $label) {
            $result [] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $result;
    }
}
