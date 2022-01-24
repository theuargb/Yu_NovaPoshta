<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Yu\NovaPoshta\Api\Data\StreetInterface;
use Yu\NovaPoshta\Api\Data\StreetSearchResultsInterface;

interface StreetRepositoryInterface
{

    /**
     * Save Street
     *
     * @param StreetInterface $street
     *
     * @return StreetInterface
     * @throws LocalizedException
     */
    public function save(StreetInterface $street);

    /**
     * Retrieve Street
     *
     * @param int $streetId
     *
     * @return StreetInterface
     * @throws LocalizedException
     */
    public function get(int $streetId);

    /**
     * Retrieve Street matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return StreetSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $cityRef
     * @return StreetSearchResultsInterface
     * @throws LocalizedException
     */
    public function getListByCityRef(string $cityRef);

    /**
     * @param string $cityRef
     * @param null|string $text
     *
     * @return string
     * @throws LocalizedException
     */
    public function getJsonByCityRef(string $cityRef, ?string $text = null);

    /**
     * Delete Street
     *
     * @param StreetInterface $street
     *
     * @return bool true on success
     *
     * @throws LocalizedException
     */
    public function delete(StreetInterface $street);

    /**
     * Delete Street by ID
     *
     * @param int $streetId
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $streetId);
}

