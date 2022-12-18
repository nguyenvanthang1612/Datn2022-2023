<?php

namespace Magenest\TrackingOrder\Controller\Index;

use Magento\Framework\App\Action\Context;

class Search extends \Magento\Framework\App\Action\Action
{
    /**
    * Page Factory
    *
    * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $ksPageFactory;

    /**
    * @var \Magento\Framework\Registry
    */
    protected $ksRegistry;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $ksMessageManager;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $ksOrderFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $ksSearchCriteriaBuilder;
    /**
     * @var \Magenest\TrackingOrder\Block\Index
     */
    protected $trackingOrderIndex;

    /**
     * @param \Magento\Framework\App\Action\Context $ksContext
     * @param \Magento\Framework\Registry $ksRegistry
     * @param \Magento\Framework\Message\ManagerInterface $ksMessageManager
     * @param \Magento\Framework\View\Result\PageFactory $ksPageFactory
     * @param \Magento\Sales\Model\OrderFactory $ksOrderFactory
     * @param \Magento\Sales\Model\OrderRepository $ksOrderRepository
     * @param \Magenest\TrackingOrder\Block\Index $trackingOrderIndex
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $ksSearchCriteriaBuilder
     */
    public function __construct(
        Context $ksContext,
        \Magento\Framework\Registry $ksRegistry,
        \Magento\Framework\Message\ManagerInterface $ksMessageManager,
        \Magento\Framework\View\Result\PageFactory $ksPageFactory,
        \Magento\Sales\Model\OrderFactory $ksOrderFactory,
        \Magento\Sales\Model\OrderRepository $ksOrderRepository,
        \Magenest\TrackingOrder\Block\Index $trackingOrderIndex,
        \Magento\Framework\Api\SearchCriteriaBuilder $ksSearchCriteriaBuilder
    ) {
        $this->ksRegistry = $ksRegistry;
        $this->ksMessageManager = $ksMessageManager;
        $this->ksPageFactory = $ksPageFactory;
        $this->ksOrderFactory = $ksOrderFactory;
        $this->ksOrderRepository = $ksOrderRepository;
        $this->ksSearchCriteriaBuilder = $ksSearchCriteriaBuilder;
        $this->trackingOrderIndex = $trackingOrderIndex;
        parent::__construct($ksContext);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id  = $this->getRequest()->getPost('order_id');
        $ksData  = $this->getOrderId($id);
        if ($ksData) {
            $ksOrderId = $this->getOrderId($id);
        } else {
            $ksOrderId = $id;
        }
        $ksEmailId = $this->getRequest()->getPost('email_address');
        $ksOrderData = $this->ksOrderFactory->create()->load($ksOrderId);
        $ksOrderItems = $ksOrderData->getAllItems();
        if (!$ksOrderItems) {
            $this->ksMessageManager->addErrorMessage(__('Please enter valid order id'));
            $ksResultRedirect = $this->resultRedirectFactory->create();
            return $ksResultRedirect->setPath('trackingorder/index/index');
        }
        if (!$this->trackingOrderIndex->checkCustomerLogin() == false) {
            if ($ksEmailId != $ksOrderData->getCustomerEmail()) {
                $this->ksMessageManager->addErrorMessage(__('Please enter correct email id'));
                $ksResultRedirect = $this->resultRedirectFactory->create();
                return $ksResultRedirect->setPath('trackingorder/index/index');
            }
        }
        $this->ksRegistry->register('orderdata', $ksOrderData);
        $this->ksResultPage = $this->ksPageFactory->create();
        $this->ksResultPage->getConfig()->getTitle()->set((__('Track Order')));
        return $this->ksResultPage;
    }

    /**
     * return order Id
     * @return  int
     */
    public function getOrderId($ksIncrementId)
    {
        $ksSearchCriteriaBuilder = $this->ksSearchCriteriaBuilder
        ->addFilter('increment_id', $ksIncrementId);
        $ksOrder = $this->ksOrderRepository->getList($ksSearchCriteriaBuilder->create())->getItems();
        foreach ($ksOrder as $key => $ksValue) {
            return $ksValue->getId();
        }
    }
}
