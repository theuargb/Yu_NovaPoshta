<?php

namespace Yu\NovaPoshta\Model\ResourceModel\Warehouse;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Yu\NovaPoshta\Model\ResourceModel\Warehouse;

class Collection extends AbstractCollection
{

    protected function _construct(): void
    {
        $this->_init(
            \Yu\NovaPoshta\Model\Warehouse::class,
            Warehouse::class
        );
    }

}
