<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_routine extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_routine
 */

namespace Magenest\Ahamove\Controller\Adminhtml\RecallDriver;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magenest\Ahamove\Model\Carrier\Ahamove;
use Magento\Sales\Api\OrderRepositoryInterface;
class Index extends Action
{
    /**
     * @var Ahamove
     */
    protected $ahamove;

    /**
     * @var
     */
    protected $orderRepository;

    /**
     * Index constructor.
     * @param Context $context
     * @param Ahamove $ahamove
     */
    public function __construct(
        Context $context,
        Ahamove $ahamove,
        OrderRepositoryInterface $orderRepository
    )
    {
        parent::__construct($context);
        $this->ahamove = $ahamove;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        $params = $this->ahamove->getApiParameters($order);
        $this->ahamove->createOrder($params, $order);

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
