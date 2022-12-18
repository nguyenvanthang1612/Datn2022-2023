<?php

namespace Magenest\GiaoHangNhanh\Helper\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package Magenest\GiaoHangNhanh\Helper\Logger
 */
class Handler extends Base
{
	/**
	 * @var string
	 */
	protected $fileName = '/var/log/shipment/ghn.log';

	/**
	 * @var int
	 */
	protected $loggerType = \Monolog\Logger::DEBUG;
}
