<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Model\ResourceModel\Street;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'street_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Yu\NovaPoshta\Model\Street::class,
            \Yu\NovaPoshta\Model\ResourceModel\Street::class
        );
    }
}

