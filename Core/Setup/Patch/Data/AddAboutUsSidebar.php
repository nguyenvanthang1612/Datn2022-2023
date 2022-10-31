<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddAboutUsSidebar extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('about-us-sidebar', 'Magenest_Core::cms/about-us-sidebar.phtml', 'About Us Sidebar');
    }
}
