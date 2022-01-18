<?php

namespace Yu\NovaPoshta\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Yu\NovaPoshta\Api\CityRepositoryInterface;
use Yu\NovaPoshta\Api\Data\CityInterface;
use Yu\NovaPoshta\Api\Data\CitySearchResultsInterfaceFactory;
use Yu\NovaPoshta\Model\ResourceModel\City\CollectionFactory;

class CityRepository implements CityRepositoryInterface
{

    /**
     * @var CityFactory
     */
    private $cityFactory;

    /**
     * @var \Yu\NovaPoshta\Model\ResourceModel\City
     */
    private $cityResourceModel;

    /**
     * @var CollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @var CitySearchResultsInterfaceFactory
     */
    private $citySearchResultFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var sting
     */
    private $lang;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param CityFactory $cityFactory
     * @param \Yu\NovaPoshta\Model\ResourceModel\City $cityResourceModel
     * @param CollectionFactory $cityCollectionFactory
     * @param CitySearchResultsInterfaceFactory $citySearchResultFactory
     */
    public function __construct(
        SearchCriteriaBuilder                       $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        ScopeConfigInterface                 $scopeConfig,
        CityFactory                                                        $cityFactory,
        \Yu\NovaPoshta\Model\ResourceModel\City                            $cityResourceModel,
        CollectionFactory                                                  $cityCollectionFactory,
        CitySearchResultsInterfaceFactory                                  $citySearchResultFactory
    ) {

        $this->cityFactory = $cityFactory;
        $this->cityResourceModel = $cityResourceModel;
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->citySearchResultFactory = $citySearchResultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->scopeConfig = $scopeConfig;
        $this->lang = $scopeConfig->getValue(
            'carriers/novaposhta/lang',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getById($cityId)
    {
        $city = $this->cityFactory->create();
        $this->cityResourceModel->load($city, $cityId);
        return $city;
    }

    /**
     * {@inheritdoc}
     */
    public function getByRef($ref)
    {
        $city = $this->cityFactory->create();
        $this->cityResourceModel->load($city, $ref, 'ref');
        return $city;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->cityCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResult = $this->citySearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonByCityName(string $name = null)
    {
        $data = [];

        if (!empty($name) && mb_strlen($name) > 1) {
            $collection = $this->cityCollectionFactory->create();
            $collection->addFieldToFilter(
                ['name_ru', 'name_ua'],
                [
                    ['like' => $name . '%'],
                    ['like' => $name . '%'],
                ]
            );
            foreach ($collection->getItems() as $item) {
                $data[] = [
                    'id' => $item->getData('ref'),
                    'text' => $item->getData('name_' . $this->lang),
                ];
            }
        }

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CityInterface $city)
    {
        return $this->cityResourceModel->save($city);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CityInterface $city)
    {
        return $this->cityResourceModel->delete($city);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cityId)
    {
        $city = $this->getById($cityId);
        if (!empty($city->getId())) {
            return $this->delete($city);
        }
        return false;
    }

}
