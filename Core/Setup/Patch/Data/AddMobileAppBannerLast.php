<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddMobileAppBannerLast extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('app.banner.last', 'Magenest_Core::cms/mobile-app-banner-last.phtml', 'App banner last');
    }
}
