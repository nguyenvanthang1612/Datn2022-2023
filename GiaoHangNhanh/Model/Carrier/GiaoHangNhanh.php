<?php

namespace Magenest\GiaoHangNhanh\Model\Carrier;

use Magenest\GiaoHangNhanh\Helper\CityProvinceProvider;
use Magenest\GiaoHangNhanh\Helper\DistrictGhnHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Xml\Security;
use Magento\Inventory\Model\ResourceModel\Source;
use Magento\Inventory\Model\SourceFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as ResourceOrder;

//use Magenest\ShopByBrand\Model\BrandFactory;
//use Magenest\ShopByBrand\Model\ResourceModel\Brand;
/**
 * Class GiaoHangNhanh
 * @package Magenest\GiaoHangNhanh\Model\Carrier
 */
class GiaoHangNhanh extends \Magento\Shipping\Model\Carrier\AbstractCarrierOnline implements
    \Magento\Shipping\Model\Carrier\CarrierInterface,
    \Magenest\GiaoHangNhanh\Api\GiaoHangNhanhInterface
{
    const SHOP_PAID = 1;

    const CUSTOMER_PAID = 2;
    /**
     * @var string
     */
    protected $_code = 'giaohangnhanh';
    /**
     * @var bool
     */
    protected $_isFixed = true;
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var SourceFactory
     */
    protected $sourceFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $inventoryResource;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $quoteItemFactory;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item
     */
    protected $itemResourceModel;
    /**
     * @var CityProvinceProvider
     */
    protected $cityProvinceProvider;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var DistrictGhnHelper
     */
    protected $districtHelper;
    /**
     * @var ResourceOrder
     */
    protected $resourceOrder;
    /**
     * @var OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    //	/**
    //	 * @var \Magenest\Core\Helper\OrderHelper
    //	 */
    //	protected $orderHelper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    protected $shipmentFee = 0;

    /**
     * GiaoHangNhanh constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param Source $inventoryResource
     * @param SourceFactory $sourceFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteItemFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote $itemResourceModel
     * @param CityProvinceProvider $cityProvinceProvider
     * @param ManagerInterface $messageManager
     * @param DistrictGhnHelper $districtHelper
     * @param ResourceOrder $resourceOrder
     * @param OrderFactory $orderFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        Source $inventoryResource,
        SourceFactory $sourceFactory,
        \Magento\Quote\Model\QuoteFactory $quoteItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote $itemResourceModel,
        CityProvinceProvider $cityProvinceProvider,
        ManagerInterface $messageManager,
        DistrictGhnHelper $districtHelper,
        ResourceOrder $resourceOrder,
        OrderFactory $orderFactory,
        \Magento\Framework\App\Request\Http $request,
//		\Magenest\Core\Helper\OrderHelper $orderHelper,
        Session $session,
        array $data = []
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->inventoryResource = $inventoryResource;
        $this->sourceFactory = $sourceFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->cityProvinceProvider = $cityProvinceProvider;
        $this->messageManager = $messageManager;
        $this->districtHelper = $districtHelper;
        $this->resourceOrder = $resourceOrder;
        $this->orderFactory = $orderFactory;
        $this->request = $request;
        //		$this->orderHelper = $orderHelper;
        $this->checkoutSession = $session;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $shippingPrice = $this->getConfigData('price');

        $result = $this->_rateFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }

            if ($request->getPackageValue() !== false) {
                $fixedPrice = 0;
                if ($this->_scopeConfig->getValue('carriers/giaohangnhanh/price_type') == "1") {
                    $fixedPrice = $this->_scopeConfig->getValue('carriers/giaohangnhanh/total_fee');
                    $shippingPrice = $fixedPrice;
                }
                if ($this->_scopeConfig->getValue('carriers/giaohangnhanh/enable_freeshipping')) {
                    $subtotal = $this->_scopeConfig->getValue('carriers/giaohangnhanh/freeshipping_subtotal');
                    if ($subtotal <= $request->getPackageValue()) {
                        $shippingPrice = 0.0;
                    }
                }
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
            $this->checkoutSession->setActiveShippingMethod($this->_code);
            $result->append($method);

            if ($shippingPrice) {
                /** @var \Magento\Framework\App\State $state */
                $state = ObjectManager::getInstance()->get('\Magento\Framework\App\State');
                try {
                    if ($state->getAreaCode() === "adminhtml") {
                        $method = $this->_rateMethodFactory->create();

                        $method->setCarrier($this->_code);
                        $method->setCarrierTitle($this->getConfigData('title'));

                        $method->setMethod($this->_code . "-free");
                        $method->setMethodTitle($this->getConfigData('name') . " FREESHIP");
                        $method->setPrice(0.0);
                        $method->setCost(0.0);
                        $result->append($method);
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * estimate price shipping
     * @param int $cartId
     * @param string $street
     * @param string $region
     * @param string $city
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     * @throws \Zend_Http_Client_Exception
     */
    public function estimateShipping($cartId, $street, $region, $city)
    {
        $priceType = $this->_scopeConfig->getValue('carriers/giaohangnhanh/price_type');
        /** get weight */
        $weight = 0;
        $poundToKg =  0.4535;
        $checkBrand = false;
        $quoteItem = $this->quoteItemFactory->create();
        $this->itemResourceModel->load($quoteItem, $cartId);
        $products = $quoteItem->getAllItems();
        foreach ($products as $product) {
            if ($product->getWeight()) {
                $weight += ($product->getWeight() * $poundToKg * $product->getQty() * 1000);
            }
        }
        /** Get config data */
        $token = $this->_scopeConfig->getValue('carriers/giaohangnhanh/api_token');
        /** get from district and to district */
        $toDistrictId = $this->getDistrictId($region, $city, $token);
        $sourceItem = $this->sourceFactory->create();
        $this->inventoryResource->load($sourceItem, 'default', 'source_code');
        $fromDistrictId = $this->getSourceData($sourceItem->getRegion(), $sourceItem->getCity(), $token);
        /** get all service */
        $result = [];
        $serviceArray = $this->getAllAvailableServiceApi($token, (int)$weight, $fromDistrictId, $toDistrictId);
        if (!$serviceArray) {
            return $result;
        }
        $serviceId = $this->getSmallestPriceShip($serviceArray);
        $freeShipping = $this->_scopeConfig->getValue('carriers/giaohangnhanh/enable_freeshipping');
        $freeShippingPrice = $this->_scopeConfig->getValue('carriers/giaohangnhanh/freeshipping_subtotal');
        $enableFreeBrand = $this->_scopeConfig->getValue('carriers/giaohangnhanh/enable_freebrand');
        if ($enableFreeBrand) {
            $checkBrand = $this->checkBrand($quoteItem);
        }
        if ($freeShipping && $quoteItem->getSubtotal() >= $freeShippingPrice || $checkBrand) {
            return [
                'day_ship' => number_format($freeShippingPrice) . ' VND',
                'service_name' => number_format($freeShippingPrice) . ' VND',
                'ServiceID' => $serviceId,
                'ServiceFee' => 0,
                'freeShip' => true,
                'samePrice' => false,
                'date_ship' => ''
            ];
        }
        if (!$priceType) {
            if (!empty($serviceArray)) {
                foreach ($serviceArray['data'] as &$service) {
                    $service['day_ship'] = __('Delivery on ');
                    $service['freeShip'] = false;
                    $service['samePrice'] = false;
                    $service['service_name'] = $service['Name'];
                    $service['date_ship'] = date('d/m/Y', strtotime($service['ExpectedDeliveryTime']));
                }
                $result = $serviceArray['data'];
            }
        } else {
            $fixedPrice = $this->_scopeConfig->getValue('carriers/giaohangnhanh/total_fee');
            $result[] = [
                'day_ship' => __('Flat Delivery Fee'),
                'service_name' => __("Flat Delivery Fee"),
                'ServiceID' => $serviceId,
                'freeShip' => false,
                'ServiceFee' => $fixedPrice,
                'samePrice' => true,
                'date_ship' => ''
            ];
        }

        return $result;
    }

    /**
     * @param string $region
     * @param string$city
     * @param string $token
     * @return int
     * @throws \Zend_Http_Client_Exception
     */
    public function getDistrictId($region, $city, $token)
    {
        $districtId = 0;
        $districtArray = $this->districtHelper->getDistrictData();
        $cityData = $this->cityProvinceProvider->getCityById($city);
        $cityName = is_array($cityData) ? $cityData['name'] : $city;
        if ($cityName) {
            $region = $this->cityProvinceProvider->stripVN(mb_strtolower($region, 'UTF-8'));
            $cityName = $this->cityProvinceProvider->stripVN(mb_strtolower($cityName, 'UTF-8'));
            foreach ($districtArray as $district) {
                $provinceName = $this->cityProvinceProvider->stripVN(mb_strtolower($district['ProvinceName'], 'UTF-8'));
                $miningText = $this->cityProvinceProvider->stripVN(mb_strtolower($district['MiningText'], 'UTF-8'));
                $districtName = $this->cityProvinceProvider->stripVN(mb_strtolower($district['DistrictName'], 'UTF-8'));
                if ($districtName && $region) {
                    if ((strstr($districtName, $region) != false || strstr($region, $districtName) != false)
                        && (strstr($provinceName, $cityName) != false || strstr($cityName, $provinceName) != false)) {
                        $districtId = $district['DistrictID'];
                        break;
                    }
                }

                if ((!$miningText || !$provinceName)) {
                    continue;
                } else {
                    if (($provinceName == $cityName || strstr($provinceName, $cityName) != false || strstr($cityName, $provinceName) != false)  &&
                        (strstr($miningText, $region) != false)) {
                        $districtId = $district['DistrictID'];
                        break;
                    }
                }
            }
        }
        return $districtId;
    }

    /**
     * Get all available service of district
     * @param string $token
     * @param int $weight
     * @param int $formDistrictId
     * @param int $toDistrictId
     * @return array|mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function getAllAvailableServiceApi($token, $weight, $formDistrictId, $toDistrictId)
    {
        if ($formDistrictId == 0 || $toDistrictId == 0) {
            return [];
        }
        $param = [
            "token" => $token,
            "weight" => $weight ? $weight : 1000,
            "FromDistrictID" => $formDistrictId,
            "ToDistrictID" => $toDistrictId
        ];
        $uri = "https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district";
        $response = $this->callApi($param, $uri);
        if (!empty($response)) {
            $response = json_decode($this->callApi($param, $uri), true);
            if (!isset($response['data']) && $response['message'] != 'Success') {
                return [];
            }
        }
        return $response;
    }

    public function getSmallestPriceShip($services)
    {
        $min = 0;
        $service_key = 0;
        if (isset($services['data'])) {
            foreach ($services['data'] as $service) {
                if (!$min) {
                    $min = $service['ServiceFee'] ?? 0;
                    $service_key = $service['ServiceID'] ?? 0;
                } elseif ($min > $service['ServiceFee']) {
                    $min = $service['ServiceFee'] ?? 0;
                    $service_key = $service['ServiceID'] ?? 0;
                }
            }
            $this->shipmentFee = $min;
            return $service_key;
        }
        return false;
    }

    /**
     * Call API
     * @param $param
     * @param $uri
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function callApi($param, $uri)
    {
//        $client = $this->httpClientFactory->create();
//        $client->setUri($uri);
//        $client->setMethod(\Zend_Http_Client::POST);
//        $client->setHeaders(\Zend_Http_Client::CONTENT_TYPE, 'application/json');
//        $client->setHeaders('Accept', 'application/json');
//        $client->setHeaders('token', '181c1778-521d-11ed-b26c-02ed291d830a');
//        $client->setParameterPost($param);
//        //$client->setRawData(json_encode($param, true));
//        return $client->request()->getBody();
        $data_string = json_encode($param);
        $curl = curl_init($uri);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'token: 181c1778-521d-11ed-b26c-02ed291d830a',
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        return curl_exec($curl);
    }

    /**
     * Get source data by id
     * @param $region_id
     * @param $city_id
     * @param $token
     * @return int
     * @throws \Zend_Http_Client_Exception
     */
    public function getSourceData($region_id, $city_id, $token)
    {
        $city = $this->cityProvinceProvider->getCityById($city_id);
        $district = $this->cityProvinceProvider->getProvinceById($city_id, $region_id);
        $cityName = is_array($city) ? $city['name'] : '';
        $districtName = is_array($district) ? $district['name'] : '';

        return $this->getDistrictId($districtName, $cityName, $token);
    }

    /**
     * @param $shipment
     * @param $weight
     * @param $length
     * @param $width
     * @param $height
     * @param $type
     * @param $result
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Zend_Http_Client_Exception
     */
    public function createOrder($shipment, $weight, $length, $width, $height, $type, $result)
    {
        /** @var Order $order */
        $order = $shipment->getOrder();
        /** Get config value */
        $token = $this->_scopeConfig->getValue('carriers/giaohangnhanh/api_token');
        $noteCode = $this->_scopeConfig->getValue('carriers/giaohangnhanh/note_code');
        $config_length = $this->_scopeConfig->getValue('carriers/giaohangnhanh/length');
        $config_width = $this->_scopeConfig->getValue('carriers/giaohangnhanh/width');
        $config_height = $this->_scopeConfig->getValue('carriers/giaohangnhanh/height');
        /** get District */
        $sourceItem = $this->sourceFactory->create();
        $this->inventoryResource->load($sourceItem, 'default', 'source_code');
        //		$fromDistrictId = $this->getSourceData($sourceItem->getRegion(), $sourceItem->getCity(), $token);
        //		$toDistrictId = $this->getDistrictId($order->getShippingAddress()->getRegion(), $order->getShippingAddress()->getCity(), $token);
        $fromDistrictId = '1442';
        $toDistrictId ="1443";
        $serviceArray = $this->getAllAvailableServiceApi($token, (int)$weight, $fromDistrictId, $toDistrictId);
        if (!$serviceArray) {
            return $result->setErrors("Create Giao Hang Nhanh Order Fail: Address is not supported");
        }

        $message = "Số lượng {$shipment->getTotalQty()}";
        $telephone = $order->getShippingAddress()->getTelephone();
        if ($telephone[0] != "0") {
            $telephone = "0" . $telephone;
        }
        $clientTelephone = $sourceItem->getSourcePhone();
        if (isset($clientTelephone) && $clientTelephone[0] != "0") {
            $clientTelephone = "0" . $clientTelephone;
        }

        $serviceId = $this->getSmallestPriceShip($serviceArray);

        $codAmount = $order->getPayment()->getMethod() == "checkmo" ? (int)$order->getGrandTotal() : 0;

        if ($order->getPayment()->getMethod() != "checkmo") {
            $type = self::SHOP_PAID;
        }

        if ($type == self::CUSTOMER_PAID && $codAmount > $this->shipmentFee) {
            $codAmount -= $this->shipmentFee;
        }

//        $params = [
//            "token" => $token,
//            "PaymentTypeID" => 1,
//            "FromDistrictID" => $fromDistrictId,
//            "ToDistrictID" => $toDistrictId,
//            "ExternalCode" => $order->getIncrementId() ?: '',
//            "Note" => $message . ($order->getCustomerNote() ? ' - ' . $order->getCustomerNote() : ""),
//            "ClientContactName"=> $sourceItem->getName() ? $sourceItem->getName() : "Elise",
//            "ClientContactPhone" => $clientTelephone,
//            "ClientAddress" => $sourceItem->getStreet(),
//            "to_name" => $order->getCustomerName(),
//            "ToPhone" => $telephone,
//            "ToAddress"=> $order->getShippingAddress()->getStreet()[0],
//            "CoDAmount" => $codAmount,
//            "RequiredCode"=> $noteCode,
//            "ServiceID" => (int)($order->getShippingServiceId() ?: $serviceId),
//            "FromLat" => $sourceItem->getLatitude() ? $sourceItem->getLatitude() : "",
//            "FromLng" => $sourceItem->getLongitude() ? $sourceItem->getLongitude() : "",
//            "Content"=> $order->getCustomerNote() ? $order->getCustomerNote() : "",
//            "Weight" => $weight ? $weight : 1000,
//            "Length"=> $length ? (int)$length : (int)$config_length,
//            "Width"=> $width ? (int)$width : (int)$config_width,
//            "Height"=> $height ? (int)$height : (int)$config_height,
//            "ReturnContactName"=> $sourceItem->getName(),
//            "ReturnContactPhone"=> $clientTelephone,
//            "ReturnAddress"=> $sourceItem->getStreet(),
//            "ReturnDistrictID"=> $fromDistrictId,
//            "ExternalReturnCode"=> $sourceItem->getName(),
//            "PaymentTypeId" => (int)$type
//        ];
        $items = $shipment->getItems();
        $pItems= [];
        foreach ($items as $item) {
            $pItems[] = [
                "name"=> $item->getName(),
                "code"=> $item->getSku(),
                "quantity"=> $item->getData('qty'),
                "price"=> (int)$item->getPrice(),
                "length"=> 12,
                "width"=> 12,
                "height"=> 12
            ];
        }
        $params = [
            "payment_type_id"=> (int)$type,
            "note"=> $message . ($order->getCustomerNote() ? ' - ' . $order->getCustomerNote() : ""),
            "from_name"=> $sourceItem->getContactName() ? $sourceItem->getName() : "Store",
            "from_phone"=>$sourceItem->getPhone(),
            "from_address"=>$sourceItem->getStreet(),
            "from_ward_name"=>"Phường Ô Chợ Dừa",
            "from_district_name"=>"Quận Đống Đa",
            "from_province_name"=>"TP Hà Nội",
            "required_note"=> $noteCode,
            "return_name"=> $sourceItem->getContactName() ? $sourceItem->getName() : "Store",
            "return_phone"=> $sourceItem->getPhone(),
            "return_address"=> $sourceItem->getStreet(),
            "return_ward_name"=> "Phường Ô Chợ Dừa",
            "return_district_name"=> "Quận Đống Đa",
            "return_province_name"=>"TP Hà Nội",
            "client_order_code"=> "",
            "to_name"=> $order->getCustomerName(),
            "to_phone"=> $telephone,
            "to_address"=> $order->getShippingAddress()->getStreet()[0],
            "to_ward_name"=>"Phường Tràng Tiền",
            "to_district_name"=>"Quận Hoàn Kiếm",
            "to_province_name"=>"TP Hà Nội",
            "cod_amount"=> $codAmount,
            "content"=> $order->getCustomerNote() ? $order->getCustomerNote() : "",
            "weight"=> 200,
            "length"=> 1,
            "width"=> 19,
            "height"=> 10,
            "pick_station_id"=> 1444,
            "deliver_station_id"=> null,
            "insurance_value"=> (int)$order->getGrandTotal(),
            "service_id"=> (int)($order->getShippingServiceId() ?: $serviceId),
            "service_type_id"=>2,
            "coupon"=>null,
            "pick_shift"=>null,
            "pickup_time"=> 1665272576,
            "items"=>$pItems
        ];

        if ($order->getGrandTotal() <= 10000000) {
            $params['insurance_value'] = (int)$order->getGrandTotal();
        }
        $uri = 'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create';
        $response = json_decode($this->callApi($params, $uri), true);
        if ($response['message'] == 'Success') {
            $orderCode = $response['data']['client_order_code'];
            $order->setData('api_order_id', $orderCode);
            $order->setData('shipment_type', $type);
            $order->setData('shipment_fee', $this->shipmentFee);
//            $order->setData('transfer_amount', $codAmount);
            $this->resourceOrder->save($order, 'ReadyToPick');
            //			$this->orderHelper->afterShipmentSms($order);
            $this->messageManager->addSuccessMessage("Create Giao Hang Nhanh Order Success");
        } else {
            $result->setErrors("Create Giao Hang Nhanh Order Fail: " . $response['message']);
        }
    }

    /**
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Zend_Http_Client_Exception
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $result = new \Magento\Framework\DataObject();
        $poundToKg =  0.4535;
        $weight = 0;
        $length = 0;
        $width = 0;
        $height = 0;
        $type = 0;
        $result->setTrackingNumber($request->getOrderShipment()->getOrderId());
        $result->setShippingLabelContent('GHN Service');
        if (!$request->getOrderShipment()->getOrder()->getApiOrderId()) {
            $packages = $request->getPackages();
            foreach ($packages as $package) {
                if ($package['params']['weight']) {
                    $weight += ($package['params']['weight_units'] ==  'POUND') ? ($package['params']['weight'] * $poundToKg)
                        : $package['params']['weight'] * 1000;
                }
                $length = $package['params']['length'];
                $width = $package['params']['width'];
                $height = $package['params']['height'];
                $type = $package['params']['shipment_type'];
            }
            $this->createOrder($request->getOrderShipment(), $weight, $length, $width, $height, $type, $result);
        }
        return $result;
    }

    /**
     * @param array $data
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateShipmentStatus($data = [])
    {
        if (!empty($data) && isset($data['OrderCode'])) {
            $orderCode = $data['OrderCode'];
            $order = $this->orderFactory->create();
            $this->resourceOrder->load($order, $orderCode, 'api_order_id');
            if ($data['Status'] == "Delivered" && $order->getPayment()->getMethod() == "checkmo") {
                $dateTime = ObjectManager::getInstance()->get(TimezoneInterface::class);
                $order->setTransferDate($dateTime->date()->format('Y-m-d H:i:s'));
                $codAmount = $order->getGrandTotal();
                if ($order->getShipmentType() == self::CUSTOMER_PAID) {
                    $codAmount -= $order->getShipmentFee();
                }
                $order->setTransferAmount($codAmount);
            }
            $this->resourceOrder->save($order, $data['CurrentStatus']);
        }
    }

    /**
     * @param $params
     * @throws \Zend_Http_Client_Exception
     */
    public function cancelOrder($params)
    {
        $uri = "https://dev-online-gateway.ghn.vn/shiip/public-api/v2/switch-status/cancel";
        $this->callApi($params, $uri);
    }

    public function processAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return true;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function checkBrand($quote)
    {
        $freeBrand = $this->_scopeConfig->getValue('carriers/giaohangnhanh/free_brand');
        $brandIds = explode(',', $freeBrand);
        foreach ($quote->getAllItems() as $item) {
            if (!$item->getParentItemId()) {
                if (!in_array($item->getProduct()->getBrandId(), $brandIds)) {
                    return false;
                }
            }
        }
        return true;
    }
}
