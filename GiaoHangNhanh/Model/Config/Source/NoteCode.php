<?php

namespace Magenest\GiaoHangNhanh\Model\Config\Source;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class NoteCode
 * @package Magenest\Ahamove\Model\Config\Source
 */
class NoteCode implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => "CHOTHUHANG", 'label' => __('Cho khách hàng thử hàng')],
            ['value' => "CHOXEMHANGKHONGTHU", 'label' => __('Cho khách hàng xem hàng, không cho thử')],
            ['value' => "KHONGCHOXEMHANG", 'label' => __('Không cho khách hàng xem hàng')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            "CHOTHUHANG" => __('Cho khách hàng thử hàng'),
            "CHOXEMHANGKHONGTHU" => __('Cho khách hàng xem hàng, không cho thử'),
            "KHONGCHOXEMHANG" => __('Không cho khách hàng xem hàng')
        ];
    }
}
