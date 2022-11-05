<?php
/**
 * Created by PhpStorm.
 * User: kal
 * Date: 28/03/2020
 * Time: 16:28
 */

namespace Magenest\Ahamove\Model\Config\Source\Api;

use Magenest\Ahamove\Helper\ShipmentHelper;
use Magento\Framework\Option\ArrayInterface;
use Magento\Inventory\Model\ResourceModel\Source\CollectionFactory;
use Magenest\Ahamove\Model\RegionModel;
use Magenest\Ahamove\Model\ApiConnect;

/**
 * Class Service
 *
 * @package Magenest\Ahamove\Model\Config\Source\Api
 */
class Service implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ShipmentHelper
     */
    protected $shipmentHelper;
    /**
     * @var ApiConnect
     */
    protected $apiConnect;

    /**
     * Service constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param ShipmentHelper    $shipmentHelper
     * @param ApiConnect        $apiConnect
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ShipmentHelper $shipmentHelper,
        ApiConnect $apiConnect
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->shipmentHelper    = $shipmentHelper;
        $this->apiConnect    = $apiConnect;
    }

    public function toOptionArray()
    {
        return $this->getServices();
    }

    public function getServices()
    {
        $data = [];
        $result = $this->getCollectionSourceCode();
        if (!empty($result)) {
            foreach ($result as $item) {
                $data[] = [
                    'value' => $item['_id'],
                    'label' => "{$item['_id']}: {$item['name_vi_vn']}"
                ];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getCollectionSourceCode()
    {
        $result = [];
        $collection = $this->collectionFactory->create()->addFieldToFilter('source_code', ['in' => array_keys(RegionModel::REGION_SOURCE_CITY)]);
        foreach ($collection as $source) {
            if ($source->getLatitude() && $source->getLongitude()) {
                $data        = [
                    "lat" => $source->getLatitude(),
                    "lng" => $source->getLongitude()
                ];
                $this->shipmentHelper->debug("Services List Request: " . var_export($data, true));
                try {
                    $response = $this->apiConnect->serviceTypes($data);
                } catch (\Throwable $e) {
                    $this->shipmentHelper->debug($e);
                    $response = false;
                }
                if (is_array($response)) {
                    $this->shipmentHelper->debug("Services List Response: " . var_export($response, true));
                    if (!empty($response)) {
                        foreach ($response as $res) {
                            $result[$res['_id']] = $res;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
