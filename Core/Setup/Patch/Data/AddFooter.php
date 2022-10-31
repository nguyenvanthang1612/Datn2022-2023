<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddFooter extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('footer_links', 'Magenest_Core::cms/footer.phtml', 'Footer');
    }
}
