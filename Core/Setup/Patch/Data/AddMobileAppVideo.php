<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddMobileAppVideo extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('app.video', 'Magenest_Core::cms/mobile-app-video.phtml', 'App video');
    }
}
