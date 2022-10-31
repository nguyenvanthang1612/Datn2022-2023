<?php

namespace Magenest\RegionStock\Plugin\Product;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Model\Product;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;

class CheckSalableRegionQty
{
    /**
     * @var \Magenest\RegionStock\Helper\Helper
     */
    protected $helper;

    /**
     * @var \Magenest\RegionStock\Api\GetRegionSalableQtyInterface
     */
    protected $getRegionSalableQty;

    /**
     * @var GetStockIdForCurrentWebsite
     */
    private $getStockIdForCurrentWebsite;

    private $stockId = null;

    /**
     * CheckSalableRegionQty constructor.
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param \Magenest\RegionStock\Helper\Helper $helper
     * @param \Magenest\RegionStock\Api\GetRegionSalableQtyInterface $getRegionSalableQty
     */
    public function __construct(
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        \Magenest\RegionStock\Helper\Helper $helper,
        \Magenest\RegionStock\Api\GetRegionSalableQtyInterface $getRegionSalableQty
    ) {
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->helper = $helper;
        $this->getRegionSalableQty = $getRegionSalableQty;
    }

    /**
     * @param Product $subject
     * @param $result
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterIsSalable(
        Product $subject,
        $result
    ) {
        if ($subject->getTypeId() != BundleType::TYPE_CODE) {
            $cookie = $this->helper->getCookie();
            if ($this->helper->isEnableRegionStock() && $cookie) {
                $this->stockId = $this->getStockIdForCurrentWebsite->execute();
                $regionQty = (float)$this->getRegionSalableQty->execute($subject->getSku(), $cookie, $this->stockId);
                $result = $regionQty > 0;
            }
        }
        return $result;
    }
}
