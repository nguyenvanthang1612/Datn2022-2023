<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\RegionStock\Api;

interface GetRegionSalableQtyInterface
{
    /**
     * Get Product Quantity for given SKU and Stock
     *
     * @param string $sku
     * @param string $regionId
     * @param string $stockId
     * @return float
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $sku, string $regionId, string $stockId): ?float;
}
