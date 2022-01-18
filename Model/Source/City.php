<?php

namespace Yu\NovaPoshta\Model\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;
use Yu\NovaPoshta\Model\ResourceModel\City\CollectionFactory;

class City implements GenericInterface
{
    /**
     * @var CollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @param CollectionFactory $cityCollectionFactory
     */
    public function __construct(
        CollectionFactory $cityCollectionFactory
    ) {
        $this->cityCollectionFactory = $cityCollectionFactory;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $cityCollection = $this->cityCollectionFactory->create();

        $options = [];

        foreach ($cityCollection as $city) {
            $options[] = [
                'value' => $city->getData('ref'),
                'label' => $city->getData('name_ru') . ', ' . $city->getData('type_ru'),
            ];
        }

        return $options;
    }
}
