<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddTopHeader extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('top_header', 'Magenest_Core::cms/top-header.phtml', 'Top Header');
    }
}
