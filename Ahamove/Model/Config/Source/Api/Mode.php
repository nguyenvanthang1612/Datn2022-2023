<?php
/**
 * Created by PhpStorm.
 * User: kal
 * Date: 10/02/2020
 * Time: 08:31
 */

namespace Magenest\Ahamove\Model\Config\Source\Api;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Mode
 * @package Magenest\Ahamove\Model\Config\Source\Api
 */
class Mode implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Staging')],
            ['value' => 1, 'label' => __('Production')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Staging'), 1 => __('Production')];
    }
}
