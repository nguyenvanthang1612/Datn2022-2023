<?php


namespace Magenest\Core\Setup\Patch\Data;


class AddCheckoutContact extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('checkout.contact', 'Magenest_Core::cms/checkout-contact.phtml', 'Checkout Contact');
    }
}
