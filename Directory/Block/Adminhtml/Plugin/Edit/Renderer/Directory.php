<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Block\Adminhtml\Plugin\Edit\Renderer;

use Magenest\Directory\Helper\Data;
use Magento\Backend\Block\Template;
use Magento\Customer\Model\Address;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Directory extends Template implements RendererInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magenest_Directory::order/address/directory.phtml';

    /**
     * @var array
     */
    protected $_formValues = [];

    /**
     * @var string
     */
    protected $_htmlIdPrefix = '';

    /**
     * @var string
     */
    protected $_htmlNamePrefix = '';

    /**
     * @var string
     */
    protected $_htmlPrefix = '';

    /**
     * @var string
     */
    protected $_htmlSuffix = '';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     * @param Template\Context $context
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        Data $helper,
        Template\Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getDataJson()
    {
        return $this->helper->getDataJson();
    }

    /**
     * @param int $addressId
     *
     * @return array|Address
     */
    public function getAddressById($addressId)
    {
        return $this->helper->getAddressById($addressId);
    }

    /**
     * @param int $quoteId
     *
     * @return mixed
     */
    public function getQuoteAddressById($quoteId)
    {
        return $this->helper->getQuoteAddressById($quoteId);
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        if (strlen($this->_htmlNamePrefix)) {
            $this->_htmlPrefix = '[';
            $this->_htmlSuffix = ']';
        }

        return $this->toHtml();
    }

    /**
     * Get field id
     *
     * @param $id
     *
     * @return string
     */
    public function getFieldId($id)
    {
        return $this->getHtmlIdPrefix() . $id;
    }

    /**
     * Get html id prefix
     *
     * @return string
     */
    public function getHtmlIdPrefix()
    {
        return $this->_htmlIdPrefix;
    }

    /**
     * Set html id prefix
     *
     * @param $htmlIdPrefix
     *
     * @return $this
     */
    public function setHtmlIdPrefix($htmlIdPrefix)
    {
        $this->_htmlIdPrefix = $htmlIdPrefix;

        return $this;
    }

    /**
     * Get field name
     *
     * @param $name
     *
     * @return string
     */
    public function getFieldName($name)
    {
        return $this->getHtmlNamePrefix()
            . $this->getHtmlPrefix()
            . $name
            . $this->getHtmlSuffix();
    }

    /**
     * Get html name prefix
     *
     * @return string
     */
    public function getHtmlNamePrefix()
    {
        return $this->_htmlNamePrefix;
    }

    /**
     * Set html name prefix
     *
     * @param $htmlNamePrefix
     *
     * @return $this
     */
    public function setHtmlNamePrefix($htmlNamePrefix)
    {
        $this->_htmlNamePrefix = $htmlNamePrefix;

        return $this;
    }

    /**
     * Get html prefix
     *
     * @return string
     */
    public function getHtmlPrefix()
    {
        return $this->_htmlPrefix;
    }

    /**
     * Set html prefix
     *
     * @param $htmlPrefix
     *
     * @return $this
     */
    public function setHtmlPrefix($htmlPrefix)
    {
        $this->_htmlPrefix = $htmlPrefix;

        return $this;
    }

    /**
     * Set html suffix
     *
     * @return string
     */
    public function getHtmlSuffix()
    {
        return $this->_htmlSuffix;
    }

    /**
     * Set html suffix
     *
     * @param $htmlSuffix
     *
     * @return $this
     */
    public function setHtmlSuffix($htmlSuffix)
    {
        $this->_htmlSuffix = $htmlSuffix;

        return $this;
    }

    /**
     * Get form values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->_formValues;
    }

    /**
     * Set form values
     *
     * @param $formValues
     *
     * @return $this
     */
    public function setFormValues($formValues)
    {
        $this->_formValues = $formValues;

        return $this;
    }
}
