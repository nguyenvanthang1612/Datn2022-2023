<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddFlashSaleBanner extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('flash.sale.banner', 'Magenest_Core::cms/flash-sale-banner.phtml', 'Flash sale banner');
    }
}
