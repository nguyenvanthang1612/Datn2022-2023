<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Block\Adminhtml\Ward;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        $ward = $this->registry->registry('current_ward');
        if ($ward && $ward->getId()) {
            return __("Edit Ward '%1'", $this->escapeHtml($ward->getName()));
        } else {
            return __('New Ward');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_Directory';
        $this->_controller = 'adminhtml_ward';
        parent::_construct();
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ]
        );
    }
}
