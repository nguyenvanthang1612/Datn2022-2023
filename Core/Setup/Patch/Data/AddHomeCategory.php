<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddHomeCategory extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('home.category.slider', 'Magenest_Core::cms/home-category.phtml', '', 'Home Category Slider ');
    }
}
