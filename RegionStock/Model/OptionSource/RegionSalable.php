<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Richs extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Richs
 */

namespace Magenest\RegionStock\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magenest\RegionPopup\Model\OptionSource\Region;

class RegionSalable implements OptionSourceInterface
{
    const HANOI_SALABLE_ATTR = 'hanoi_salable';
    const DANANG_SALABLE_ATTR = 'danang_salable';
    const HCMC_SALABLE_ATTR = 'hcmc_salable';
    const CANTHO_SALABLE_ATTR = 'cantho_salable';

    const REGION_SALABLE_MAPPING = [
         Region::HANOI_REGION => self::HANOI_SALABLE_ATTR,
         Region::DANANG_REGION => self::DANANG_SALABLE_ATTR,
         Region::HCM_REGION => self::HCMC_SALABLE_ATTR,
         Region::CANTHO_REGION => self::CANTHO_SALABLE_ATTR,
    ];

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

    public function getAllOptions()
    {
        return [
            self::HANOI_SALABLE_ATTR => __('Ha Noi'),
            self::DANANG_SALABLE_ATTR => __('Da Nang'),
            self::HCMC_SALABLE_ATTR => __('Ho Chi Minh'),
            self::CANTHO_SALABLE_ATTR => __('Can Tho'),
        ];
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $key => $option) {
            if ($key == $value) {
                return $option;
            }
        }

        return "";
    }
}
