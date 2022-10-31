<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddQuickLink extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('quick_link', 'Magenest_Core::cms/quick_link.phtml', 'Quick Link');
    }
}
