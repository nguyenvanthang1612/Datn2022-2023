<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddMobileAppBenefit extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('app.benefit', 'Magenest_Core::cms/mobile-app-benefit.phtml', 'App Benefit');
    }
}
