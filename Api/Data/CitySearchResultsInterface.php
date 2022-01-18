<?php

namespace Yu\NovaPoshta\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CitySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get cities list.
     *
     * @return CityInterface[]
     */
    public function getItems();

    /**
     * Set cities list.
     *
     * @param CityInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
