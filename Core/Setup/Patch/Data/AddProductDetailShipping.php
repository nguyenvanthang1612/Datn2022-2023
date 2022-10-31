<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddProductDetailShipping extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('product.detail.shipping.method', 'Magenest_Core::cms/product-detail-shipping.phtml', 'Product Detail Shipping Method');
    }
}
