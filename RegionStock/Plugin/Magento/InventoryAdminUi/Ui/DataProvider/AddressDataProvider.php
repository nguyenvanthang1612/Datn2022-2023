<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Rich's extension
 * NOTICE OF LICENSE
 *
 * @author TrangHa
 * @category Magenest
 * @package Magenest_Rich's
 * @Date 08/07/2021
 */

namespace Magenest\RegionStock\Plugin\Magento\InventoryAdminUi\Ui\DataProvider;

use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class AddressDataProvider
{
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    public function __construct(SourceRepositoryInterface $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param SourceDataProvider $subject
     * @param $data
     * @return array
     */
    public function afterGetData(
        SourceDataProvider $subject,
        $data
    ): array {
        $searchCriteria = $subject->getSearchCriteria();
        $result = $this->sourceRepository->getList($searchCriteria)->getItems();
        if (isset($data['items'])) {
            foreach ($data['items'] as $key => $item) {
                $originalSource = $result[$item['source_code']];
                $data['items'][$key]['city_id'] = $originalSource->getCityId();
            }
            return $data;
        }
        $attributes = ['city', 'city_id', 'district', 'district_id', 'ward', 'ward_id'];
        foreach ($data as $key => &$source) {
            $originalSource = $result[$key];
            foreach ($attributes as $attribute) {
                $source['general'][$attribute] = $originalSource->getData($attribute) ? : "";
            }
        }
        return $data;
    }
}
