<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Yu\NovaPoshta\Api\Data\StreetInterface;
use Yu\NovaPoshta\Api\Data\StreetInterfaceFactory;
use Yu\NovaPoshta\Api\Data\StreetSearchResultsInterfaceFactory;
use Yu\NovaPoshta\Api\StreetRepositoryInterface;
use Yu\NovaPoshta\Model\ResourceModel\Street as ResourceStreet;
use Yu\NovaPoshta\Model\ResourceModel\Street\CollectionFactory as StreetCollectionFactory;
use Yu\NovaPoshta\Service\Curl;

class StreetRepository implements StreetRepositoryInterface
{

    /**
     * @var Street
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceStreet
     */
    protected $resource;

    /**
     * @var StreetCollectionFactory
     */
    protected $streetCollectionFactory;

    /**
     * @var StreetInterfaceFactory
     */
    protected $streetFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Curl
     */
    private $curl;


    /**
     * @param ResourceStreet $resource
     * @param StreetInterfaceFactory $streetFactory
     * @param StreetCollectionFactory $streetCollectionFactory
     * @param StreetSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceStreet                      $resource,
        StreetInterfaceFactory              $streetFactory,
        StreetCollectionFactory             $streetCollectionFactory,
        StreetSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface        $collectionProcessor,
        SearchCriteriaBuilder               $searchCriteriaBuilder,
        Curl                                $curl
    ) {
        $this->resource = $resource;
        $this->streetFactory = $streetFactory;
        $this->streetCollectionFactory = $streetCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->curl = $curl;
    }

    /**
     * @inheritDoc
     */
    public function save(StreetInterface $street)
    {
        try {
            $this->resource->save($street);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the street: %1',
                $exception->getMessage()
            ));
        }
        return $street;
    }

    /**
     * @inheritDoc
     */
    public function get($streetId)
    {
        $street = $this->streetFactory->create();
        $this->resource->load($street, $streetId);
        if (!$street->getId()) {
            throw new NoSuchEntityException(__('Street with id "%1" does not exist.', $streetId));
        }
        return $street;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->streetCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByCityRef(string $cityRef)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(StreetInterface::CITY_REF, $cityRef)->create();
        return $this->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonByCityRef(string $cityRef, ?string $text = null)
    {
        $params = ['modelName' => 'Address', 'calledMethod' => 'getStreet', "methodProperties" =>
            [
                "CityRef" => $cityRef,
                "FindByString" => $text,
            ],
        ];

        $data = $this->curl->getDataImport($params);

        if (!$data) {
            return json_encode([]);
        }

        $warehouseData = [];
        foreach ($data as $_data) {
            $warehouseData[] = [
                'id' => $_data['Ref'],
                'ref' => $_data['Ref'],
                'text' => $_data['StreetsType'] . ' ' . $_data['Description'],
            ];
        }

        return json_encode($warehouseData);
    }

    /**
     * @inheritDoc
     */
    public function delete(StreetInterface $street)
    {
        try {
            $streetModel = $this->streetFactory->create();
            $this->resource->load($streetModel, $street->getStreetId());
            $this->resource->delete($streetModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Street: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($streetId)
    {
        return $this->delete($this->get($streetId));
    }
}

