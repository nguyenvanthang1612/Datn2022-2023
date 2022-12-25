<?php


namespace Magenest\StoreLocator\Controller\Adminhtml\Locator;

class Grid extends \Magento\Backend\App\Action {

    /**
     * Managing store locator grid
     *
     * @return void
     */
    public function execute() {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

}
