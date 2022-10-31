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

namespace Magenest\Core\Model\Http\Client;

/**
 * Class Curl
 *
 * @package Magenest\Core\Model\Http\Client
 */
class Curl extends \Magento\Framework\HTTP\Client\Curl
{
    private $sslVersion;

    /**
     * Curl constructor.
     *
     * @param null $sslVersion
     */
    public function __construct($sslVersion = null)
    {
        $this->sslVersion = $sslVersion;

        return parent::__construct($sslVersion);
    }

    /**
     * Make PUT request
     *
     * String type was added to parameter $param in order to support sending JSON or XML requests.
     * This feature was added base on Community Pull Request https://github.com/magento/magento2/pull/8373
     *
     * @param string       $uri
     * @param array|string $params
     *
     * @return void
     *
     * @see \Magento\Framework\HTTP\Client#post($uri, $params)
     */
    public function put($uri, $params)
    {
        $this->makePutRequest($uri, $params);
    }

    protected function makePutRequest($uri, $params)
    {
        $this->_ch = curl_init();
        $this->curlOption(CURLOPT_URL, $uri);
        $this->curlOption(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->curlOption(CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);

        if (count($this->_headers)) {
            $heads = [];
            foreach ($this->_headers as $k => $v) {
                $heads[] = $k . ': ' . $v;
            }
            $this->curlOption(CURLOPT_HTTPHEADER, $heads);
        }

        if (count($this->_cookies)) {
            $cookies = [];
            foreach ($this->_cookies as $k => $v) {
                $cookies[] = "{$k}={$v}";
            }
            $this->curlOption(CURLOPT_COOKIE, implode(";", $cookies));
        }

        if ($this->_timeout) {
            $this->curlOption(CURLOPT_TIMEOUT, $this->_timeout);
        }

        if ($this->_port != 80) {
            $this->curlOption(CURLOPT_PORT, $this->_port);
        }

        $this->curlOption(CURLOPT_RETURNTRANSFER, 1);
        $this->curlOption(CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);
        if ($this->sslVersion !== null) {
            $this->curlOption(CURLOPT_SSLVERSION, $this->sslVersion);
        }

        if (count($this->_curlUserOptions)) {
            foreach ($this->_curlUserOptions as $k => $v) {
                $this->curlOption($k, $v);
            }
        }

        $this->_headerCount     = 0;
        $this->_responseHeaders = [];
        $this->_responseBody    = curl_exec($this->_ch);
        $err                    = curl_errno($this->_ch);
        if ($err) {
            $this->doError(curl_error($this->_ch));
        }
        curl_close($this->_ch);
    }
}
