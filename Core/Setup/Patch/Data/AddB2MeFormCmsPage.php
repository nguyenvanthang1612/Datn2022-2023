<?php


namespace Magenest\Core\Setup\Patch\Data;


use Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch;

class AddB2MeFormCmsPage extends AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('b2me','Magenest_Core::b2me.phtml', "B2Me",'B2Me Form');
    }
}
