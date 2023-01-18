<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Block\Adminhtml\City\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Directory\Model\Config\Source\Country as CountrySource;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends Generic
{
    /**
     * @var CountrySource
     */
    protected $countrySource;

    /**
     * @param CountrySource $countrySource
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        CountrySource $countrySource,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->countrySource = $countrySource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = $this->_formFactory->create();
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setMethod('post');
        $city = $this->retrieveModel();
        $dashboard = $form->addFieldset('base_fieldset', ['legend' => __('City Information')]);
        if ($city->getId()) {
            $dashboard->addField('city_id', 'hidden', ['name' => 'id']);
        }
        $disabled = !empty($city->getId());
        $dashboard->addField(
            'country_id',
            'select',
            [
                'name'     => 'country_id',
                'label'    => __('Country'),
                'title'    => __('Country'),
//                'values'   => $this->countrySource->toOptionArray(),
                'values'   => $this->toOptionArray(),
                'disabled' => $disabled,
                'required' => true
            ]
        );
        $dashboard->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'title'    => __('Name'),
                'label'    => __('Name'),
                'required' => true,
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
                'disabled' => $disabled,
                'class' => 'validate-not-negative-number'
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
        $form->setValues($city->getData());
        $this->setForm($form);
    }

    /**
     * @return mixed|null
     */
    public function retrieveModel()
    {
        return $this->_coreRegistry->registry('current_city');
    }

    public function toOptionArray() {
        $option = [
            ['value' => 'VN', 'label' => 'Việt Nam']

        ];
        return $option;
    }
}
