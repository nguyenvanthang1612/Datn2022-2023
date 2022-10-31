<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddB2MeBusinessPage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('b2me-business', 'Magenest_Core::cms/b2me-business.phtml', '', 'B2Me - Business Hub for F&B Business');
    }
}
