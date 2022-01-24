<?php
/**
 * Copyright © - All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Yu\NovaPoshta\Api\Data;

interface StreetInterface
{
    const STREET_ID = 'street_id';

    const REF = 'ref';

    const CITY_REF = 'city_ref';

    const NAME_UA = 'name_ua';

    const NAME_RU = 'name_ru';

    const STREETS_TYPE_REF = 'streets_type_ref';

    const STREETS_TYPE = 'streets_type';

    /**
     * Get street_id
     *
     * @return int|null
     */
    public function getStreetId();

    /**
     * Set street_id
     *
     * @param int $streetId
     *
     * @return $this
     */
    public function setStreetId(int $streetId);

    /**
     * Get ref
     * @return string|null
     */
    public function getRef();

    /**
     * Set ref
     * @param string $ref
     * @return $this
     */
    public function setRef(string $ref);

    /**
     * Get city_ref
     * @return string|null
     */
    public function getCityRef();

    /**
     * Set city_ref
     * @param string $cityRef
     * @return $this
     */
    public function setCityRef(string $cityRef);

    /**
     * Get name_ua
     * @return string|null
     */
    public function getNameUa();

    /**
     * Set name_ua
     * @param string $nameUa
     * @return $this
     */
    public function setNameUa(string $nameUa);

    /**
     * Get name_ru
     * @return string|null
     */
    public function getNameRu();

    /**
     * Set name_ru
     * @param string $nameRu
     * @return $this
     */
    public function setNameRu(string $nameRu);

    /**
     * Get streets_type_ref
     * @return string|null
     */
    public function getStreetsTypeRef();

    /**
     * Set streets_type_ref
     * @param string $streetsTypeRef
     * @return $this
     */
    public function setStreetsTypeRef(string $streetsTypeRef);

    /**
     * Get streets_type
     * @return string|null
     */
    public function getStreetsType();

    /**
     * Set streets_type
     * @param string $streetsType
     * @return $this
     */
    public function setStreetsType(string $streetsType);
}

