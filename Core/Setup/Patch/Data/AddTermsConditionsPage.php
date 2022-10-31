<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddTermsConditionsPage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('terms-conditions', 'Magenest_Core::cms/terms-conditions.phtml', '', 'Điều khoản và dịch vụ');
    }
}
