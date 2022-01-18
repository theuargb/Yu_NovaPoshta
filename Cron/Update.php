<?php

namespace Yu\NovaPoshta\Cron;

use Yu\NovaPoshta\Model\Import\CityImport;
use Yu\NovaPoshta\Model\Import\WarehouseImport;

/**
 * Синхронизация (обновление) городов и отделений сохраненных в базе с novaposhta.ua
 * Рекомендуется выполнять синхронизацию 1 раз в сутки
 */
class Update
{

    /**
     * @var CityImport
     */
    private $cityImport;

    /**
     * @var WarehouseImport
     */
    private $warehouseImport;

    /**
     * @param CityImport $cityImport
     * @param WarehouseImport $warehouseImport
     */
    public function __construct(
        CityImport      $cityImport,
        WarehouseImport $warehouseImport
    ) {
        $this->cityImport = $cityImport;
        $this->warehouseImport = $warehouseImport;
    }

    /**
     *
     */
    public function execute()
    {
        $this->cityImport->execute();
        $this->warehouseImport->execute();
    }

}
