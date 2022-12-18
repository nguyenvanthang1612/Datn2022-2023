<?php

namespace Magenest\TrackingOrder\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $ksPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $ksContext
     * @param \Magento\Framework\View\Result\PageFactory $ksPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $ksContext,
        \Magento\Framework\View\Result\PageFactory $ksPageFactory
    ) {
        $this->ksPageFactory = $ksPageFactory;
        return parent::__construct($ksContext);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->ksResultPage = $this->ksPageFactory->create();
        $this->ksResultPage->getConfig()->getTitle()->set((__('Track Order')));
        return $this->ksResultPage;
    }
}
