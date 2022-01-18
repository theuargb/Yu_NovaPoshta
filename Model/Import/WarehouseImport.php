<?php

namespace Yu\NovaPoshta\Model\Import;

use Closure;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Yu\NovaPoshta\Model\ResourceModel\Warehouse;
use Yu\NovaPoshta\Model\ResourceModel\Warehouse\CollectionFactory;
use Yu\NovaPoshta\Model\WarehouseFactory;
use Yu\NovaPoshta\Service\Curl;

/**
 * Import warehouses from novaposhta.ua
 *
 * @author Yu
 */
class WarehouseImport
{

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var WarehouseFactory
     */
    private $warehouseFactory;

    /**
     * @var Warehouse
     */
    private $warehouseResource;

    /**
     * @var CollectionFactory
     */
    private $warehouseCollectionFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Curl $curl
     * @param WarehouseFactory $warehouseFactory
     * @param Warehouse $warehouseResource
     * @param CollectionFactory $warehouseCollectionFactory
     */
    public function __construct(
        ScopeConfigInterface             $scopeConfig,
        Curl                                    $curl,
        WarehouseFactory                          $warehouseFactory,
        Warehouse                   $warehouseResource,
        CollectionFactory $warehouseCollectionFactory
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->warehouseFactory = $warehouseFactory;
        $this->warehouseResource = $warehouseResource;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
    }

    /**
     * @param Closure $cl
     *
     * @return void
     */
    public function execute(Closure $cl = null)
    {
        $warehousesFromNovaPoshta = $this->importWarehouses();
        if ($warehousesFromNovaPoshta == null) {
            if (is_callable($cl)) {
                $cl('Ошибка импорта отделений. Проверьте ключ API.');
                return;
            }
        }

        $warehouses = $this->getWarehousesFromDb();

        foreach ($warehousesFromNovaPoshta as $warehouseFromNovaPoshta) {
            $key = array_search($warehouseFromNovaPoshta['ref'], array_column($warehouses, 'ref'), true);

            if ($key === false || ($key !== 0 && empty($key))) {
                $this->saveWarehouse($warehouseFromNovaPoshta);
            } else if (isset($warehouses[$key]['warehouse_id'])) {

                if (
                    ($warehouses[$key]['ref'] !== $warehouseFromNovaPoshta['ref']) ||
                    ($warehouses[$key]['name_ua'] !== $warehouseFromNovaPoshta['name_ua']) ||
                    ($warehouses[$key]['name_ru'] !== $warehouseFromNovaPoshta['name_ru']) ||
                    ($warehouses[$key]['city_ref'] !== $warehouseFromNovaPoshta['city_ref']) ||
                    ($warehouses[$key]['number'] !== $warehouseFromNovaPoshta['number'])
                ) {
                    $warehouseId = $warehouses[$key]['warehouse_id'];
                    $this->saveWarehouse($warehouseFromNovaPoshta, $warehouseId);
                }
            }

            if ($cl !== null) {
                $cl($warehouseFromNovaPoshta['ref'] . ' ' . $warehouseFromNovaPoshta['name_ru']);
            }
        }
    }

    /**
     * @param array $data
     * @param int|null $warehouseId
     */
    private function saveWarehouse(array $data, $warehouseId = null)
    {
        $warehouse = $this->warehouseFactory->create();
        $warehouse->setWarehouseId($warehouseId);
        $warehouse->setRef($data['ref']);
        $warehouse->setCityRef($data['city_ref']);
        $warehouse->setNameUa($data['name_ua']);
        $warehouse->setNameRu($data['name_ru']);
        $warehouse->setNumber($data['number']);
        $this->warehouseResource->save($warehouse);
    }

    /**
     * Return Warehouses array
     *
     * @return array
     */
    private function getWarehousesFromDb()
    {
        $warehouseCollection = $this->warehouseCollectionFactory->create();

        $data = $warehouseCollection->load()->toArray();
        return $data['items'];
    }

    /**
     * @return array | null
     */
    private function importWarehouses()
    {
        $params = ['modelName' => 'AddressGeneral', 'calledMethod' => 'getWarehouses'];

        $data = $this->curl->getDataImport($params);

        if ($data) {
            $warehouseData = [];
            foreach ($data as $_data) {
                $warehouseData[] = [
                    'ref' => $_data['Ref'],
                    'city_ref' => $_data['CityRef'],
                    'name_ua' => $_data['Description'],
                    'name_ru' => $_data['DescriptionRu'],
                    'number' => $_data['Number'],
                ];
            }
            return $warehouseData;
        } else {
            return null;
        }
    }

}
