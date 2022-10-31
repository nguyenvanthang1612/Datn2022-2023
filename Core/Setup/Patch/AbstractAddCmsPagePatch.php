<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Abbott extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Abbott
 */

namespace Magenest\Core\Setup\Patch;

use Magento\Framework\App\Area;
use Magento\Framework\Setup\ModuleDataSetupInterface;

abstract class AbstractAddCmsPagePatch implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    protected $pageFactory;

    protected $pageRepository;

    protected $pageResource;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $dataSetup;

    protected $state;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Cms\Model\ResourceModel\Page $pageResource,
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\App\State $state
    ) {
        $this->state = $state;
        $this->dataSetup = $moduleDataSetup;
        $this->_objectManager = $objectManager;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->pageResource = $pageResource;
        $this->checkArea();
    }

    protected function checkArea()
    {
        try {
            $this->state->getAreaCode();
        } catch (\Exception $e) {
            $this->state->setAreaCode(Area::AREA_GLOBAL);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    public function updateCmsPage($identifier, $templatePath, $pageHeading, $title = null, $pageLayout = '1column')
    {
        if (empty($title)) {
            $title = $identifier;
        }
        $template = $this->_objectManager->get(\Magento\Framework\View\Element\Template::class);
        $content = $template->setTemplate($templatePath)->toHtml();
        $data = [
            'is_active' => 1,
            'title' => $title,
            'content_heading' => $pageHeading,
            'content' => $content,
            'identifier' => $identifier,
            'stores' => [0],
            'page_layout' => $pageLayout,
            'sort_order' => 0
        ];

        /** @var \Magento\Cms\Model\Page $pageModel */
        $pageModel = $this->pageFactory->create();
        $pageId = $this->pageResource->checkIdentifier($data['identifier'], 0);
        if ($pageId) {
            $this->pageResource->load($pageModel, $pageId);
            $pageModel->addData($data);
        } else {
            $pageModel->setStoreId(0)->setData($data);
        }
        $this->pageRepository->save($pageModel);
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->dataSetup->startSetup();
        $this->doUpdate();
        $this->dataSetup->endSetup();
    }

    public abstract function doUpdate();
}
