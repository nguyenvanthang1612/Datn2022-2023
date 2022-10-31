<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddMembershipPage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('membership', 'Magenest_Core::cms/membership.phtml', '', 'Chính sách quản lý điểm');
    }
}
