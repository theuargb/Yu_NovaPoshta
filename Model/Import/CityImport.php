<?php

namespace Yu\NovaPoshta\Model\Import;

use Closure;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Yu\NovaPoshta\Model\CityFactory;
use Yu\NovaPoshta\Model\ResourceModel\City;
use Yu\NovaPoshta\Model\ResourceModel\City\CollectionFactory;
use Yu\NovaPoshta\Service\Curl;

/**
 * Import cities from novaposhta.ua
 *
 * @author Yu
 */
class CityImport
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
     * @var CityFactory
     */
    private $cityFactory;

    /**
     * @var \Yu\NovaPoshta\Model\City\ResourceModel\City
     */
    private $cityResource;

    /**
     * @var CollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Curl $curl
     * @param CityFactory $cityFactory
     * @param City $cityResource
     * @param CollectionFactory $cityCollectionFactory
     */
    public function __construct(
        ScopeConfigInterface        $scopeConfig,
        Curl                               $curl,
        CityFactory                          $cityFactory,
        City                   $cityResource,
        CollectionFactory $cityCollectionFactory
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->cityFactory = $cityFactory;
        $this->cityResource = $cityResource;
        $this->cityCollectionFactory = $cityCollectionFactory;
    }

    /**
     * @param Closure $cl
     *
     * @return void
     */
    public function execute(Closure $cl = null)
    {
        $citiesFromNovaPoshta = $this->importCities();
        if ($citiesFromNovaPoshta == null) {
            if (is_callable($cl)) {
                $cl('Ошибка импорта городов. Проверьте ключ API.');
                return;
            }
        }

        $cities = $this->getCitiesFromDb();

        foreach ($citiesFromNovaPoshta as $cityFromNovaPoshta) {
            $key = array_search($cityFromNovaPoshta['ref'], array_column($cities, 'ref'), true);

            if ($key === false || ($key !== 0 && empty($key))) {
                $this->saveCity($cityFromNovaPoshta);
            } else if (isset($cities[$key]['city_id'])) {

                if (
                    ($cities[$key]['ref'] !== $cityFromNovaPoshta['ref']) ||
                    ($cities[$key]['name_ua'] !== $cityFromNovaPoshta['name_ua']) ||
                    ($cities[$key]['name_ru'] !== $cityFromNovaPoshta['name_ru']) ||
                    ($cities[$key]['area'] !== $cityFromNovaPoshta['area']) ||
                    ($cities[$key]['type_ua'] !== $cityFromNovaPoshta['type_ua']) ||
                    ($cities[$key]['type_ru'] !== $cityFromNovaPoshta['type_ru'])
                ) {
                    $cityId = $cities[$key]['city_id'];
                    $this->saveCity($cityFromNovaPoshta, $cityId);
                }
            }

            if (is_callable($cl)) {
                $cl($cityFromNovaPoshta['ref'] . ' ' . $cityFromNovaPoshta['name_ru']);
            }
        }
    }

    /**
     * @param array $data
     * @param int|null $cityId
     */
    private function saveCity(array $data, $cityId = null)
    {
        $cityModel = $this->cityFactory->create();
        $cityModel->setCityId($cityId);
        $cityModel->setRef($data['ref']);
        $cityModel->setNameUa(($data['name_ua'] ? $data['name_ua'] : $data['name_ru']));
        $cityModel->setNameRu(($data['name_ru'] ? $data['name_ru'] : $data['name_ua']));
        $cityModel->setArea($data['area']);
        $cityModel->setTypeUa($data['type_ua']);
        $cityModel->setTypeRu($data['type_ru']);
        $this->cityResource->save($cityModel);
    }

    /**
     * Return cities array
     *
     * @return array
     */
    private function getCitiesFromDb()
    {
        $cityCollection = $this->cityCollectionFactory->create();

        $data = $cityCollection->load()->toArray();
        return $data['items'];
    }

    /**
     * @return array | null
     */
    private function importCities()
    {
        $params = ['modelName' => 'Address', 'calledMethod' => 'getCities'];

        $data = $this->curl->getDataImport($params);

        if ($data) {
            $cityData = [];
            foreach ($data as $_data) {
                $cityData[] = [
                    'ref' => $_data['Ref'],
                    'name_ua' => $_data['Description'],
                    'name_ru' => $_data['DescriptionRu'],
                    'area' => isset($_data['Area']) ? $_data['Area'] : '',
                    'type_ua' => isset($_data['SettlementTypeDescription']) ? $_data['SettlementTypeDescription'] : '',
                    'type_ru' => isset($_data['SettlementTypeDescriptionRu']) ? $_data['SettlementTypeDescriptionRu'] : '',
                ];
            }
            return $cityData;
        } else {
            return null;
        }
    }

}
