<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Block\Adminhtml\District\Edit;

use Magenest\Directory\Helper\DirectoryHelper;
use Magenest\Directory\Model\City;
use Magenest\Directory\Model\ResourceModel\City\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends Generic
{
    /**
     * @var CollectionFactory
     */
    protected $cityCollectionFactory;

    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    /**
     * @param CollectionFactory $cityCollectionFactory
     * @param DirectoryHelper $directoryHelper
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        CollectionFactory $cityCollectionFactory,
        DirectoryHelper $directoryHelper,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->directoryHelper = $directoryHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = $this->_formFactory->create();
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $district = $this->retrieveModel();
        $dashboard = $form->addFieldset('base_fieldset', ['legend' => __('District Information')]);
        if ($district->getId()) {
            $dashboard->addField('district_id', 'hidden', ['name' => 'id']);
        }
        $disabled = !empty($district->getId());
        $dashboard->addField(
            'city_id',
            'select',
            [
                'name'     => 'city_id',
                'label'    => __('City'),
                'title'    => __('City'),
                'values'   => $this->cityCollectionFactory->create()->toOptionArray(),
                'required' => true,
                'disabled' => $disabled
            ]
        );
        if ($district->getId() && $district->getCityId()) {
            $dashboard->addField(
                'is_city_disabled',
                'select',
                [
                    'name'     => 'is_city_disabled',
                    'label'    => __('Is City Disabled On Storefront'),
                    'title'    => __('Is City Disabled On Storefront'),
                    'values'   => $this->directoryHelper->isDisableOnStoreFront(
                        City::TABLE,
                        'city_id',
                        $district->getCityId()
                    ),
                    'required' => false,
                    'disabled' => true
                ]
            );
        }
        $dashboard->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'title'    => __('Name'),
                'label'    => __('Name'),
                'required' => true
            ]
        );
        $dashboard->addField(
            'default_name',
            'text',
            [
                'name'     => 'default_name',
                'title'    => __('Full Name'),
                'label'    => __('FullName'),
                'required' => true
            ]
        );
        $dashboard->addField(
            'code',
            'text',
            [
                'name'     => 'code',
                'title'    => __('Code'),
                'label'    => __('Code'),
                'required' => true,
                'disabled' => $disabled
            ]
        );
        $dashboard->addField(
            'disable_on_storefront',
            'select',
            [
                'name'     => 'disable_on_storefront',
                'title'    => __('Disable on storefront'),
                'label'    => __('Disable on storefront'),
                'required' => false,
                'options'  => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ]
            ]
        );
        $form->setValues($district->getData());
        $this->setForm($form);
    }

    /**
     * @return mixed|null
     */
    public function retrieveModel()
    {
        return $this->_coreRegistry->registry('current_district');
    }
}
