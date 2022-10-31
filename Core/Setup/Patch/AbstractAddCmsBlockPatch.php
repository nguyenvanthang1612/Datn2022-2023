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

abstract class AbstractAddCmsBlockPatch implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    protected $blockFactory;

    protected $blockRepository;

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
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\App\State $state
    ) {
        $this->state = $state;
        $this->dataSetup = $moduleDataSetup;
        $this->_objectManager = $objectManager;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
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

    public function updateCmsBlock($identifier, $templatePath, $title = null, $storeId = [0])
    {
        if (empty($title)) {
            $title = $identifier;
        }
        $template = $this->_objectManager->get(\Magento\Framework\View\Element\Template::class);
        $content = $template->setTemplate($templatePath)->toHtml();
        $data = [
            'title' => $title,
            'identifier' => $identifier,
            'content' => $content,
            'is_active' => 1,
            'stores' => $storeId,
            'sort_order' => 0
        ];
        try {
            $cartSummary = $this->blockRepository->getById($identifier);
        } catch (\Exception $e) {
            $cartSummary = $this->blockFactory->create();
        }
        $cartSummary->addData($data);
        $this->blockRepository->save($cartSummary);
    }

    public function createCmsBlock($identifier, $templatePath, $title = null, $storeId = [0])
    {
        if (empty($title)) {
            $title = $identifier;
        }
        if (empty($template)) {
            $content = '';
        } else {
            $template = $this->_objectManager->get(\Magento\Framework\View\Element\Template::class);
            $content = $template->setTemplate($templatePath)->toHtml();
        }
        $data = [
            'title' => $title,
            'identifier' => $identifier,
            'content' => $content,
            'is_active' => 1,
            'stores' => $storeId,
            'store_id' => $storeId[0],
            'sort_order' => 0
        ];
        $cartSummary = $this->blockFactory->create();
        $cartSummary->addData($data);
        $this->blockRepository->save($cartSummary);
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
