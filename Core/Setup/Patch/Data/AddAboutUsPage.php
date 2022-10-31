<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddAboutUsPage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('about-us', 'Magenest_Core::cms/about-us.phtml', '', 'Về chúng tôi');
    }
}
