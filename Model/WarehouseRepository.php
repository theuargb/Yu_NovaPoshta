<?php

namespace Yu\NovaPoshta\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Yu\NovaPoshta\Api\Data\WarehouseInterface;
use Yu\NovaPoshta\Api\Data\WarehouseSearchResultsInterfaceFactory;
use Yu\NovaPoshta\Api\WarehouseRepositoryInterface;
use Yu\NovaPoshta\Model\ResourceModel\Warehouse\CollectionFactory;

class WarehouseRepository implements WarehouseRepositoryInterface
{

    /**
     * @var WarehouseFactory
     */
    private $warehouseFactory;

    /**
     * @var \Yu\NovaPoshta\Model\ResourceModel\Warehouse
     */
    private $warehouseResourceModel;

    /**
     * @var CollectionFactory
     */
    private $warehouseCollectionFactory;

    /**
     * @var WarehouseSearchResultsInterfaceFactory
     */
    private $warehouseSearchResultFactory;

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
     * @var \Yu\NovaPoshta\Model\ResourceModel\City
     */
    private $resourceModelCity;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ScopeConfigInterface $scopeConfig
     * @param WarehouseFactory $warehouseFactory
     * @param \Yu\NovaPoshta\Model\ResourceModel\Warehouse $warehouseResourceModel
     * @param CollectionFactory $warehouseCollectionFactory
     * @param WarehouseSearchResultsInterfaceFactory $warehouseSearchResultFactory
     * @param \Yu\NovaPoshta\Model\ResourceModel\City $resourceModelCity
     */
    public function __construct(
        SearchCriteriaBuilder                       $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        ScopeConfigInterface                 $scopeConfig,
        WarehouseFactory                                                   $warehouseFactory,
        \Yu\NovaPoshta\Model\ResourceModel\Warehouse                       $warehouseResourceModel,
        CollectionFactory                                                  $warehouseCollectionFactory,
        WarehouseSearchResultsInterfaceFactory                             $warehouseSearchResultFactory,
        \Yu\NovaPoshta\Model\ResourceModel\City                            $resourceModelCity
    ) {
        $this->warehouseFactory = $warehouseFactory;
        $this->warehouseResourceModel = $warehouseResourceModel;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->warehouseSearchResultFactory = $warehouseSearchResultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->scopeConfig = $scopeConfig;
        $this->resourceModelCity = $resourceModelCity;
        $this->lang = $scopeConfig->getValue(
            'carriers/novaposhta/lang',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getById($warehouseId)
    {
        $warehouse = $this->warehouseFactory->create();
        $this->warehouseResourceModel->load($warehouse, $warehouseId);
        return $warehouse;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->warehouseCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResult = $this->warehouseSearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByCityRef($cityRef)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('city_ref', $cityRef)->create();
        return $this->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonByCityRef($cityRef)
    {
        $result = $this->getListByCityRef($cityRef);
        $data = [];
        foreach ($result->getItems() as $item) {
            $data[] = [
                'id' => $item->getData('warehouse_id'),
                'text' => $item->getData('name_' . $this->lang),
            ];
        }
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonByCityName($cityName)
    {
        $cityRef = $this->resourceModelCity->getRefByName($cityName);
        return $this->getJsonByCityRef($cityRef);
    }

    /**
     * {@inheritdoc}
     */
    public function save(WarehouseInterface $warehouse)
    {
        return $this->warehouseResourceModel->save($warehouse);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(WarehouseInterface $warehouse)
    {
        return $this->warehouseResourceModel->delete($warehouse);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($warehouseId)
    {
        $warehouse = $this->getById($warehouseId);
        return $this->warehouseResourceModel->delete($warehouse);
    }

}
