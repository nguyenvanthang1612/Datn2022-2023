<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddMobileAppPage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('app-mobile', 'Magenest_Core::cms/mobile-app.phtml', '', 'App mobile');
    }
}
