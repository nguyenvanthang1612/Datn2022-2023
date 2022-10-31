<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Rich's extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Rich's
 */

namespace Magenest\Ahamove\Plugin\Block\Adminhtml\Order;

use Magenest\Ahamove\Model\OrderManagement;

class View
{
    /**
     * @var \Magenest\Ahamove\Helper\Helper
     */
    protected $helper;

    /**
     * View constructor.
     * @param \Magenest\Ahamove\Helper\Helper $helper
     */
    public function __construct(\Magenest\Ahamove\Helper\Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return mixed|null
     */
    public function getOrder()
    {
        return $this->helper->registry('sales_order');
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View $view
     */
    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $view)
    {
        $order = $this->getOrder();
        if ($order->getStatus() == OrderManagement::STATUS_SHIPMENT_PROCESSING) {
            $message ='Are you sure you want to recall driver?';
            $url = $view->getUrl('ahamove/recalldriver/', ['order_id' => $view->getOrderId()]);
            $view->addButton(
                'recall_driver',
                [
                    'label' => __('Recall Driver'),
                    'class' => 'recall_driver',
                    'onclick' => "confirmSetLocation('{$message}', '{$url}')"
                ]
            );
        }
    }
}
