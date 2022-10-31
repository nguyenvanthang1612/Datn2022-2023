<?php

namespace Magenest\RegionStock\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Inventory extends Action
{
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    protected $pageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Region Inventories'));

        return $resultPage;
    }
}
