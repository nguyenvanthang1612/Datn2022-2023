<?php
/**
 * Created by PhpStorm.
 * User: kal
 * Date: 19/02/2020
 * Time: 08:11
 */

namespace Magenest\GiaoHangNhanh\Controller\Webhook;

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
     * @var \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh
     */
    protected $_helper;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\GiaoHangNhanh\Model\Carrier\GiaoHangNhanh $helper
    ) {
        $this->_request = $request;
        $this->_helper = $helper;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $content = json_decode(urldecode($this->_request->getContent()), true);
        if (is_array($content)) {
            $this->_logger->debug(urldecode($this->_request->getContent()));
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
