<?php


namespace Magenest\StoreLocator\Controller\Adminhtml\Locator;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportXml extends \Magenest\StoreLocator\Controller\Adminhtml\Locator {

    /**
     * Export locator grid to XML format
     *
     * @return ResponseInterface
     */
    public function execute() {
        $this->_view->loadLayout();
        $fileName = 'locator.xml';
        $content = $this->_view->getLayout()->getChildBlock('adminhtml.locator.grid', 'grid.export');
        return $this->_fileFactory->create(
                        $fileName, $content->getExcelFile($fileName), DirectoryList::VAR_DIR
        );
    }

}
