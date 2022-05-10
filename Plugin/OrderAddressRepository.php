<?php

namespace Yu\NovaPoshta\Plugin;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;

class OrderAddressRepository
{

    public function beforeSave(OrderAddressRepositoryInterface $subject, OrderAddressInterface $address)
    {
        $extensionAttributes = $address->getExtensionAttributes();

        try {
            $address->setData('novaposhta_city_ref', $extensionAttributes->getNovaposhtaCityRef()
                ?? $address->getData('novaposhta_city_ref'));


            $address->setData('novaposhta_warehouse_ref', $extensionAttributes->getNovaposhtaWarehouseRef()
                ?? $address->getData('novaposhta_warehouse_ref'));

            $address->setData('novaposhta_street_ref', $extensionAttributes->getNovaposhtaStreetRef()
                ?? $address->getData('novaposhta_street_ref'));
        } catch (\Exception $e) {
        }
    }
}
