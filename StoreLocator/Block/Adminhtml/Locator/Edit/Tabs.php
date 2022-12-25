<?php

namespace Magenest\StoreLocator\Block\Adminhtml\Locator\Edit;

/**
 * User page left menu
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Store Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Store Information'),
                'title' => __('Store Information'),
                'content' => $this->getLayout()->createBlock('Magenest\StoreLocator\Block\Adminhtml\Locator\Edit\Tab\Main')->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'address_section',
            [
                'label' => __('Store Address'),
                'title' => __('Store Address'),
                'content' => $this->getLayout()->createBlock('Magenest\StoreLocator\Block\Adminhtml\Locator\Edit\Tab\Address')->toHtml()
            ]
        );
        return parent::_beforeToHtml();
    }
}
