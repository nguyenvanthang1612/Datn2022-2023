<?php
/**
 * Copyright © 2021 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Project extension
 * NOTICE OF LICENSE
 *
 * @author   PhongNguyen
 * @category Magenest
 * @package  Magenest_Project
 */

namespace Magenest\Core\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RequestHelper
 *
 * @package Magenest\Core\Helper
 */
class RequestHelper extends Helper
{
    const EMAIL_OTP_REQUEST_TEMPLATE = 'otp_request_template';
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * RequestHelper constructor.
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param StoreManagerInterface                        $storeManager
     * @param Registry                                     $registry
     * @param StateInterface                               $inlineTranslation
     * @param TransportBuilder                             $transportBuilder
     * @param Context                                      $context
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        StoreManagerInterface $storeManager,
        Registry $registry,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        Context $context
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder  = $transportBuilder;
        parent::__construct($serializer, $storeManager, $registry, $context);
    }

    /**
     * @param $otp
     */
    public function sendEmailOtp($otp)
    {
        try {
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::EMAIL_OTP_REQUEST_TEMPLATE)
                ->setTemplateOptions(
                    [
                        'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(
                    [
                        'otp_request'     => $otp
                    ]
                )
                ->setFromByScope('general')
                ->addTo($this->getHumanSupportEmail())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }
}
