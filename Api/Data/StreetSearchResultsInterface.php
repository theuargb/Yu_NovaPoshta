<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface StreetSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Street list.
     * @return StreetInterface[]
     */
    public function getItems();

    /**
     * Set ref list.
     *
     * @param StreetInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}

