<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Model;

use Magento\Framework\Model\AbstractModel;
use Yu\NovaPoshta\Api\Data\StreetInterface;

class Street extends AbstractModel implements StreetInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Yu\NovaPoshta\Model\ResourceModel\Street::class);
    }

    /**
     * @inheritDoc
     */
    public function getStreetId()
    {
        return $this->getData(self::STREET_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStreetId($streetId)
    {
        return $this->setData(self::STREET_ID, $streetId);
    }

    /**
     * @inheritDoc
     */
    public function getRef()
    {
        return $this->getData(self::REF);
    }

    /**
     * @inheritDoc
     */
    public function setRef($ref)
    {
        return $this->setData(self::REF, $ref);
    }

    /**
     * @inheritDoc
     */
    public function getCityRef()
    {
        return $this->getData(self::CITY_REF);
    }

    /**
     * @inheritDoc
     */
    public function setCityRef($cityRef)
    {
        return $this->setData(self::CITY_REF, $cityRef);
    }

    /**
     * @inheritDoc
     */
    public function getNameUa()
    {
        return $this->getData(self::NAME_UA);
    }

    /**
     * @inheritDoc
     */
    public function setNameUa($nameUa)
    {
        return $this->setData(self::NAME_UA, $nameUa);
    }

    /**
     * @inheritDoc
     */
    public function getNameRu()
    {
        return $this->getData(self::NAME_RU);
    }

    /**
     * @inheritDoc
     */
    public function setNameRu($nameRu)
    {
        return $this->setData(self::NAME_RU, $nameRu);
    }

    /**
     * @inheritDoc
     */
    public function getStreetsTypeRef()
    {
        return $this->getData(self::STREETS_TYPE_REF);
    }

    /**
     * @inheritDoc
     */
    public function setStreetsTypeRef($streetsTypeRef)
    {
        return $this->setData(self::STREETS_TYPE_REF, $streetsTypeRef);
    }

    /**
     * @inheritDoc
     */
    public function getStreetsType()
    {
        return $this->getData(self::STREETS_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setStreetsType($streetsType)
    {
        return $this->setData(self::STREETS_TYPE, $streetsType);
    }
}

