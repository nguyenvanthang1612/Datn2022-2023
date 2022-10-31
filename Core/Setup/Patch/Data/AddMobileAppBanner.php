<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddMobileAppBanner extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('app.banner', 'Magenest_Core::cms/mobile-app-banner.phtml', 'App banner');
    }
}
