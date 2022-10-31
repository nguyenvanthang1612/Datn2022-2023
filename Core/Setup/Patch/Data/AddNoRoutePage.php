<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddNoRoutePage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('no-route', 'Magenest_Core::cms/no-route.phtml', '');
    }
}
