<?php

namespace Magenest\Ahamove\Model\Carrier;

use Magenest\Ahamove\Helper\ShipmentHelper;
use Magenest\Ahamove\Model\Config\Source\Api\Service as ServiceSource;
use Magenest\Ahamove\Model\RegionModel;
use Magenest\Directory\Helper\Data as DirectoryHelper;
use Magenest\Directory\Model\CityFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Xml\Security;
use Magento\Inventory\Model\ResourceModel\Source;
use Magento\Inventory\Model\SourceFactory;
use Magento\OfflinePayments\Model\Checkmo;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as ResourceOrder;

/**
 * Class Ahamove
 *
 * @package Magenest\Ahamove\Model\Carrier
 */
class Ahamove extends \Magento\Shipping\Model\Carrier\AbstractCarrierOnline implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const SHIPMENT_STATUS_COMPLETED = 'COMPLETED';
    const SHIPMENT_STATUS_FAILED = 'FAILED';
    const SHIPMENT_STATUS_CANCELLED = 'CANCELLED';
    const SHIPMENT_STATUS_ACCEPTED = 'ACCEPTED';
    const SHIPMENT_STATUS_IN_PROCESS = 'IN PROCESS';
    const SHIPMENT_SUB_STATUS_RETURNED = 'RETURNED';
    protected $_code = 'ahamove';

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
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var ResourceOrder
     */
    protected $resourceOrder;
    /**
     * @var OrderFactory
     */
    protected $orderFactory;
    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;
    /**
     * @var ShipmentHelper
     */
    protected $shipmentHelper;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;
    /**
     * @var ServiceSource
     */
    protected $_serviceSource;
    /**
     * @var CityFactory
     */
    protected $_cityFactory;
    /**
     * @var \Magento\Shipping\Model\Tracking\ResultFactory
     */
    protected $_trackFactory;
    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    protected $_trackStatusFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magenest\Ahamove\Model\ApiConnect
     */
    protected $apiConnect;

    /**
     * GiaoHangNhanh constructor.
     *
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
     * @param ManagerInterface $messageManager
     * @param ResourceOrder $resourceOrder
     * @param OrderFactory $orderFactory
     * @param CookieManagerInterface $cookieManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param ShipmentHelper $shipmentHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param ServiceSource $serviceSource
     * @param CityFactory $cityFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magenest\Ahamove\Model\ApiConnect $apiConnect
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
        ManagerInterface $messageManager,
        ResourceOrder $resourceOrder,
        OrderFactory $orderFactory,
        CookieManagerInterface $cookieManager,
        \Magento\Framework\App\Request\Http $request,
        DirectoryHelper $directoryHelper,
        ShipmentHelper $shipmentHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        ServiceSource $serviceSource,
        CityFactory $cityFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\Ahamove\Model\ApiConnect $apiConnect,
        array $data = []
    ) {
        $this->httpClientFactory = $httpClientFactory;
        $this->inventoryResource = $inventoryResource;
        $this->sourceFactory = $sourceFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->messageManager = $messageManager;
        $this->resourceOrder = $resourceOrder;
        $this->orderFactory = $orderFactory;
        $this->cookieManager = $cookieManager;
        $this->request = $request;
        $this->directoryHelper = $directoryHelper;
        $this->shipmentHelper = $shipmentHelper;
        $this->_eventManager = $eventManager;
        $this->_serviceSource = $serviceSource;
        $this->_cityFactory = $cityFactory;
        $this->_trackFactory = $trackFactory;
        $this->_trackStatusFactory = $trackStatusFactory;
        $this->storeManager = $storeManager;
        $this->apiConnect = $apiConnect;
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
            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
            $subtotal = $this->shipmentHelper->getCarrierFreeshippingSubTotal();

            if ($request->getPackageValue() !== false && $this->shipmentHelper->getCarrierPriceType() == '1') {
                $fixedPrice = $this->shipmentHelper->getCarrierTotalFee();
                $shippingPrice = $request->getFreeShipping() ? $shippingPrice : $fixedPrice;
                if ($shippingPrice > 0 && $this->shipmentHelper->getCarrierEnableFreeshipping()) {
                    if ($subtotal > $request->getPackageValue()) {
                        $shippingPrice = $fixedPrice;
                    } else {
                        $shippingPrice = 0.0;
                    }
                }
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setCarrierTitle($this->getConfigData('title'));
                $method->setMethod($this->_code);
                $method->setMethodTitle($this->getConfigData('name'));
                $method->setPrice($shippingPrice);
                $method->setCost(0);

                $result->append($method);
            } elseif ($this->shipmentHelper->getCarrierPriceType() == '0' && $request->getDestCityId() && $request->getDestDistrictId() && $request->getDestWardId()) {
                $data = $this->getEstimateOrderFee($request);
                if (!empty($data)) {
                    $services = $this->shipmentHelper->getCarrierNameService();
                    $storeCode = $this->getStoreCode($request->getStoreId());
                    foreach ($data as $item) {
                        if (!isset($item['total_price'])
                            || !isset($services[$item['_id']])
                            || (isset($item['distance']) && $item['distance'] > $this->shipmentHelper->getDistanceLimit())
                            || !$this->checkCodLimit($item['_id'], $request->getData('base_subtotal_incl_tax'))
                        ) {
                            continue;
                        }
                        if ($request->getFreeShipping() || ($this->shipmentHelper->getCarrierEnableFreeshipping() && $request->getPackageValue() > $subtotal)) {
                            $price = 0;
                        } else {
                            $price = $item['total_price'];
                        }

                        $method = $this->_rateMethodFactory->create();
                        $method->setCarrier($this->_code);
                        $method->setCarrierTitle(isset($services[$item['_id']][$storeCode]) ? $services[$item['_id']][$storeCode] : 'Ahamove');
                        $method->setMethod($item['_id']);
                        $method->setMethodTitle($this->getConfigData('title'));
                        $method->setPrice($price);
                        $method->setCost(0);

                        $result->append($method);
                    }
                }
            }

            if (empty($result->asArray()) && $this->getConfigData('showmethod')) {
                $error = $this->_rateErrorFactory->create();
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $errorMsg = $this->getConfigData('specificerrmsg');
                $error->setErrorMessage($errorMsg ? $errorMsg : __('Exceeded delivery range'));

                return $error;
            }
        }

        return $result;
    }

    /**
     * @param $storeId
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreCode($storeId)
    {
        return $this->storeManager->getStore($storeId)->getCode();
    }

    /**
     * @param $request
     *
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    protected function getEstimateOrderFee($request)
    {
        $token = $this->shipmentHelper->getCarrierApiToken();
        $sourceItem = $this->sourceFactory->create();
        $city = $this->_cityFactory->create()->load($request->getDestCityId());
        $sourceCode = $this->getSource($city->getCode());
        $this->inventoryResource->load($sourceItem, $sourceCode, 'source_code');
        $data = $this->shipmentHelper->getCarrierShippingMethod($sourceItem->getSourceCode());
        $result = [];
        if (!empty($data)) {
            $payload = [
                'token' => $token,
                'order_time' => 0,
                'path' => [
                    [
                        "lat" => $sourceItem->getLatitude(),
                        "lng" => $sourceItem->getLongitude(),
                        "address" => $this->convertAddressSource($city, $sourceItem),
                        "name" => $sourceItem->getName() ? $sourceItem->getName() : "Richs",
                        "remarks" => ""
                    ],
                    [
                        "address" => $this->convertShippingAddress($request, true),
                    ]
                ],
                'services' => $data,
                'payment_method' => 'CASH'
            ];
            $payload = $this->shipmentHelper->serialize($payload);
            $result = $this->apiConnect->estimatedFee($payload);
        }

        return $result;
    }

    /**
     * @param $cityCode
     *
     * @return string
     */
    protected function getSource($cityCode)
    {
        return array_search($cityCode, RegionModel::REGION_SOURCE_CITY);
    }

    /**
     * @param \Magenest\Directory\Model\City $city
     * @param \Magento\Inventory\Model\Source $source
     *
     * @return string
     */
    protected function convertAddressSource(
        \Magenest\Directory\Model\City $city,
        \Magento\Inventory\Model\Source $source
    ) {
        return "{$source->getStreet()} {$source->getWard()} {$source->getDistrict()} {$city->getName()}";
    }

    /**
     * @param $request
     * @param $isDest
     *
     * @return string
     */
    protected function convertShippingAddress($request, $isDest)
    {
        if ($isDest) {
            $street = $request->getDestStreet();
            $ward = $request->getDestWard();
            $district = $request->getDestDistrict();
            $city = $request->getDestCity();
        } else {
            $street = $request->getStreet()[0];
            $ward = $request->getWard();
            $district = $request->getDistrict();
            $city = $request->getCity();
        }

        return "{$street} {$ward} {$district} {$city}";
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $allMethods = $this->_serviceSource->toOptionArray();
        $arr = [];
        foreach ($allowed as $code) {
            $index = array_search($code, array_column($allMethods, 'value'));
            if ($index) {
                $arr[$code] = $allMethods[$index]['label'];
            }
        }

        return $arr;
    }

    /**
     * @param $trackings
     *
     * @return \Magento\Shipping\Model\Tracking\Result
     * @throws \Zend_Http_Client_Exception
     */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = [$trackings];
        }

        $result = $this->_trackFactory->create();
        foreach ($trackings as $tracking) {
            $order = $this->orderFactory->create()->load($tracking);
            $url = $this->getAhamoveTrackingLink($order->getApiOrderId());
            $status = $this->_trackStatusFactory->create();
            $status->setCarrier($this->_code);
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);
            $status->setUrl($url);
            $result->append($status);
        }

        return $result;
    }

    /**
     * @param $code
     *
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function getAhamoveTrackingLink($code)
    {
        $params = [
            'token' => $this->shipmentHelper->getCarrierApiToken(),
            'order_id' => $code
        ];
        $response = $this->apiConnect->sharedLink($params);

        return isset($response['shared_link']) ? $response['shared_link'] : '';
    }

    /**
     * @param $params
     * @param $order
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function createOrder($params, $order)
    {
        $response = $this->apiConnect->createOrder($params);
        if (isset($response['title']) && $response['title']) {
            $msg = $response['description'] ?? __('Create Ahamove Order Fail');
            $this->messageManager->addErrorMessage($msg);
        } else {
            $orderCode = $response['order_id'];
            $order->setData('api_order_id', $orderCode);
            $this->resourceOrder->save($order);
            $this->messageManager->addSuccessMessage(__("Create Ahamove Order Success"));
        }
    }

    /**
     * @param        $items
     * @param string $param
     *
     * @return array
     */
    public function getCartItems($items, $param = '')
    {
        $itemArray = [];
        if (!empty($items)) {
            /** @var Item $item */
            foreach ($items as $item) {
                if ($item->getProductType() == Configurable::TYPE_CODE) {
                    continue;
                }
                if ($item->getParentItem()) {
                    $price = $item->getParentItem()->getPrice();
                } else {
                    $price = $item->getPrice();
                }
                $itemArray[] = [
                    "_id" => $item->getSku(),
                    "num" => $param == 'estimate' ? $item->getQty() : $item->getQtyOrdered(),
                    "name" => $item->getName(),
                    "price" => $price
                ];
            }
        }
        return $itemArray;
    }

    /**
     * @param $order
     * @param $cod
     * @param $name
     *
     * @return array
     */
    public function getPathData($order, $cod, $name)
    {
        $paymentMethodCode = $order->getPayment() ? $order->getPayment()->getMethod() : null;

        if ($paymentMethodCode == \Magenest\OnePay\Model\Ui\DomesticConfigProvider::CODE
            || $paymentMethodCode == \Magenest\OnePay\Model\Ui\InternationalConfigProvider::CODE) {
            $cod = 0;
        }
        $shippingAddress = $order->getShippingAddress();
        $sourceItem = $this->sourceFactory->create();
        $city = $this->_cityFactory->create()->load($shippingAddress->getCityId());
        $sourceCode = $this->getSource($city->getCode());
        $this->inventoryResource->load($sourceItem, $sourceCode, 'source_code');
        $stockAddress = $this->getSourceData($sourceItem);
        return $path = [
            [
                "lat" => $sourceItem->getLatitude() ? $sourceItem->getLatitude() : "",
                "lng" => $sourceItem->getLongitude() ? $sourceItem->getLongitude() : "",
                "address" => $stockAddress,
                "mobile" => $sourceItem->getPhone(),
                "name" => $sourceItem->getName() ? $sourceItem->getName() : "Richs"
            ],
            [
                "address" => $this->convertShippingAddress($shippingAddress, false),
                "mobile" => $shippingAddress->getTelephone(),
                "name" => $name,
                "cod" => $cod
            ]
        ];
    }

    /**
     * @param $source
     *
     * @return string
     */
    public function getSourceData($source)
    {
        $allCity = $this->directoryHelper->getCityOptions();
        $allDistrict = $this->directoryHelper->getDistrictOptions();
        $cityName = '';
        $districtName = '';
        foreach ($allCity as $city) {
            if ($city['value'] == $source->getCityId()) {
                $cityName = $city['label'];
                break;
            }
        }
        foreach ($allDistrict as $district) {
            if ($district['value'] == $source->getDistrictId()) {
                $districtName = $district['label'];
                break;
            }
        }
        return "{$source->getStreet()}, {$districtName}, {$cityName}";
    }

    /**
     * @param $order
     *
     * @return array
     */
    public function getApiParameters($order)
    {
        $items = $this->getCartItems($order->getAllItems());
        $path = $this->getPathData($order, (int)$order->getGrandTotal(), $order->getCustomerName());
        $token = $this->shipmentHelper->getCarrierApiToken();
        $params = [
            "token" => $token,
            "order_time" => 0,
            "service_id" => explode('ahamove_', $order->getShippingMethod())[1],
            "promo_code" => isset($promo_code) ? $promo_code : "",
            "remarks" => $order->getCustomerNote() ? $order->getCustomerNote() : "",
            "payment_method" => 'BALANCE',
            "items" => $items,
            "path" => json_encode($path, true),
        ];
        return $params;
    }

    /**
     * @param \Magento\Framework\DataObject $request
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Zend_Http_Client_Exception
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $result = new \Magento\Framework\DataObject();
        $result->setTrackingNumber($request->getOrderShipment()->getOrderId());
        $result->setShippingLabelContent('Ahamove Service');
        $params = $this->getApiParameters($request->getOrderShipment()->getOrder());
        $this->createOrder($params, $request->getOrderShipment()->getOrder());
        return $result;
    }

    public function updateShipmentStatus($data = [])
    {
        if (!empty($data) && isset($data['_id'])) {
            $orderCode = $data['_id'];
            $order = $this->orderFactory->create();
            $this->resourceOrder->load($order, $orderCode, 'api_order_id');
            if ($order->getPayment() && $order->getPayment()->getMethod() == Checkmo::PAYMENT_METHOD_CHECKMO_CODE) {
                $order->setBillingNote($order->getGrandTotal());
            }
            if ($data['status'] == self::SHIPMENT_STATUS_COMPLETED &&
                $data['path'][1]['status'] == self::SHIPMENT_STATUS_FAILED
            ) {
                $shipmentStatus = $data['path'][1]['status'];
                if (!empty($data['sub_status']) &&
                    $data['sub_status'] == self::SHIPMENT_SUB_STATUS_RETURNED
                ) {
                    $shipmentStatus = $data['sub_status'];
                }
            } else {
                $shipmentStatus = $data['status'];
            }
            $this->_eventManager->dispatch(
                "order_shipment_status_update",
                [
                    'order' => $order,
                    'shipment_status' => $shipmentStatus
                ]
            );
            $this->resourceOrder->save($order, $data['status']);
        }
    }

    /**
     * @param $params
     */
    public function cancelOrder($params)
    {
        $this->apiConnect->cancelOrder($params);
    }

    /**
     * @param \Magento\Framework\DataObject $request
     *
     * @return bool|\Magento\Framework\DataObject|\Magento\Shipping\Model\Carrier\AbstractCarrierOnline
     */
    public function processAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * @param $serviceId
     * @param $total
     * @return bool
     */
    protected function checkCodLimit($serviceId, $total)
    {
        $maxCod = $this->getDefaultMaxCode($serviceId);
        if (!$maxCod) {
            $arrMaxCod = $this->shipmentHelper->getMaxCodService();
            $maxCod = $arrMaxCod[$serviceId] ?? 0;
        }
        if ($maxCod && $total > $maxCod) {
            return false;
        }
        return true;
    }

    /**
     * @param $serviceId
     * @return int
     */
    private function getDefaultMaxCode($serviceId)
    {
        $arr = explode('-', $serviceId);
        if (isset($arr[1])) {
            switch ($arr[1]) {
                case 'EXPRESS':
                case 'BIKE':
                    return 10000000;
                case 'POOL':
                    return 1000000;
            }
        }
        return 0;
    }
}
