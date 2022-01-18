<?php

namespace Yu\NovaPoshta\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface WarehouseSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get warehouses list.
     *
     * @return WarehouseInterface[]
     */
    public function getItems();

    /**
     * Set warehouses list.
     *
     * @param WarehouseInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
