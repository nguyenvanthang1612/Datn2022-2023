<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddHomeProductListSlider extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('home.product.list.silder', 'Magenest_Core::cms/home-product-list-slider.phtml', 'Home Product List Slider');
    }
}
