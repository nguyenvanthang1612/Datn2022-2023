<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Plugin\Magento\Sales\Block\Adminhtml\Order\Create\Form;

use Magenest\Directory\Block\Adminhtml\Plugin\Edit\Renderer\Directory;
use Magento\Framework\Data\Form;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\Address;

class AddressPlugin
{
    /**
     * After get Form
     *
     * @param Address $subject
     * @param Form $form
     *
     * @return Form
     * @throws LocalizedException
     */
    public function afterGetForm(Address $subject, Form $form)
    {
        $renderer = $subject->getLayout()->createBlock(Directory::class)
                            ->setHtmlIdPrefix($form->getHtmlIdPrefix())
                            ->setHtmlNamePrefix($form->getHtmlNamePrefix())
                            ->setFormValues($subject->getFormValues());
        $form->getElement('main')
             ->removeField('city')->removeField('city_id')
             ->removeField('district')->removeField('district_id')
             ->removeField('ward')->removeField('ward_id')
             ->removeField('region')
             ->removeField('directory')
             ->addField('directory', 'text', [], 'street')
             ->setRenderer($renderer);
        $form->getElement('country_id')->setValue('VN');
        return $form;
    }
}
