<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddShoppingPaymentDirectionPage extends \Magenest\Core\Setup\Patch\AbstractAddCmsPagePatch
{
    public function doUpdate()
    {
        $this->updateCmsPage('shopping-payment-direction', 'Magenest_Core::cms/shopping-payment-direction.phtml', '', 'Hướng dẫn mua hàng và thanh toán');
    }
}
