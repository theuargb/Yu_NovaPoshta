<?php

namespace Yu\NovaPoshta\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Yu\NovaPoshta\Api\Data\WarehouseInterface;
use Yu\NovaPoshta\Api\Data\WarehouseSearchResultsInterface;

interface WarehouseRepositoryInterface
{
    /**
     * Save warehouse.
     *
     * @param WarehouseInterface $warehouse
     *
     * @return WarehouseInterface
     * @throws LocalizedException
     */
    public function save(WarehouseInterface $warehouse);

    /**
     * Retrieve warehouse.
     *
     * @param int $warehouseId
     *
     * @return WarehouseInterface
     * @throws LocalizedException
     */
    public function getById($warehouseId);

    /**
     * Retrieve warehouse.
     *
     * @param string $cityRef
     *
     * @return WarehouseSearchResultsInterface
     * @throws LocalizedException
     */
    public function getListByCityRef($cityRef);

    /**
     * Retrieve warehouse.
     *
     * @param string $cityRef
     *
     * @return string
     * @throws LocalizedException
     */
    public function getJsonByCityRef($cityRef);

    /**
     * Retrieve warehouse.
     *
     * @param string $cityName
     *
     * @return string
     * @throws LocalizedException
     */
    public function getJsonByCityName($cityName);

    /**
     * Retrieve warehouses matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return WarehouseSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete warehouse.
     *
     * @param WarehouseInterface $warehouse
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(WarehouseInterface $warehouse);

    /**
     * Delete warehouse by ID.
     *
     * @param int $warehouseId
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($warehouseId);
}
