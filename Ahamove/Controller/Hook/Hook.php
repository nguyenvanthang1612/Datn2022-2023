<?php
/**
 * Created by PhpStorm.
 * User: kal
 * Date: 25/02/2020
 * Time: 16:26
 */

namespace Magenest\Ahamove\Controller\Hook;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Hook extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magenest\Ahamove\Model\Carrier\Ahamove
     */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magenest\Ahamove\Helper\ShipmentHelper
     */
    protected $_shipmentHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magenest\Ahamove\Model\Carrier\Ahamove $helper
     * @param \Magenest\Ahamove\Helper\ShipmentHelper $shipmentHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Ahamove\Model\Carrier\Ahamove $helper,
        \Magenest\Ahamove\Helper\ShipmentHelper $shipmentHelper
    ) {
        $this->_request = $request;
        $this->_helper = $helper;
        $this->_logger = $logger;
        $this->_shipmentHelper = $shipmentHelper;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return void
     */
    public function execute()
    {
        $content = json_decode(urldecode($this->_request->getContent()), true);
        $this->_shipmentHelper->debug('ShipmentStatusResponse: ' . var_export($content, true));
        if (is_array($content)) {
            $this->_helper->updateShipmentStatus($content);
        }
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}