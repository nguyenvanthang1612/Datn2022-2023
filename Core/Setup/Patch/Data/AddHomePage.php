<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddHomePage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('home', 'Magenest_Core::cms/homepage.phtml', '', 'Home page');
    }
}
