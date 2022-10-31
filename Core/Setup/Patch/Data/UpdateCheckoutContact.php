<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Rich's extension
 * NOTICE OF LICENSE
 *
 * @author TrangHa
 * @category Magenest
 * @package Magenest_Rich's
 * @Date 17/08/2021
 */

namespace Magenest\Core\Setup\Patch\Data;

class UpdateCheckoutContact extends \Magenest\Core\Setup\Patch\AbstractAddCmsBlockPatch
{
    public function doUpdate()
    {
        $this->updateCmsBlock('checkout.contact', 'Magenest_Core::cms/checkout-contact.phtml', 'Checkout Contact');
    }
}
