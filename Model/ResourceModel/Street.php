<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Street extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('yu_novaposhta_street', 'street_id');
    }
}

