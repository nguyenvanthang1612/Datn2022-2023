<?php

namespace Magenest\Core\Setup\Patch\Data;

class AddTermsConditionsSidebar extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('terms-conditions-sidebar', 'Magenest_Core::cms/terms-conditions-sidebar.phtml', 'Terms Conditions Sidebar');
    }
}
