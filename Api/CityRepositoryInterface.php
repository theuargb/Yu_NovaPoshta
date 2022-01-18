<?php

namespace Yu\NovaPoshta\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Yu\NovaPoshta\Api\Data\CityInterface;
use Yu\NovaPoshta\Api\Data\CitySearchResultsInterface;

interface CityRepositoryInterface
{
    /**
     * Save city.
     *
     * @param CityInterface $city
     *
     * @return CityInterface
     * @throws LocalizedException
     */
    public function save(CityInterface $city);

    /**
     * Retrieve city.
     *
     * @param int $cityId
     *
     * @return CityInterface
     * @throws LocalizedException
     */
    public function getById($cityId);

    /**
     * Retrieve city.
     *
     * @param string $ref
     *
     * @return CityInterface
     * @throws LocalizedException
     */
    public function getByRef($ref);

    /**
     * Retrieve cities matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return CitySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve cities matching name.
     *
     * @param string $name | null
     *
     * @return string | null
     * @throws LocalizedException
     */
    public function getJsonByCityName(string $name = null);

    /**
     * Delete city.
     *
     * @param CityInterface $city
     *
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CityInterface $city);

    /**
     * Delete city by ID.
     *
     * @param int $cityId
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($cityId);
}
