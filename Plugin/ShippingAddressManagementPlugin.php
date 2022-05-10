<?php

namespace Yu\NovaPoshta\Plugin;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\ShippingAddressManagement;

class ShippingAddressManagementPlugin
{

    /**
     * @param ShippingAddressManagement $subject
     * @param $cartId
     * @param AddressInterface|Address $address
     *
     * @return array
     */
    public function beforeAssign(ShippingAddressManagement $subject, $cartId, $address)
    {
        $extensionAttributes = $address->getExtensionAttributes();

        try {
            $address->setData('novaposhta_city_ref', $extensionAttributes->getNovaposhtaCityRef() ?? $address->getData('novaposhta_city_ref'));
            $address->setData('novaposhta_warehouse_ref', $extensionAttributes->getNovaposhtaWarehouseRef() ?? $address->getData('novaposhta_warehouse_ref'));
            $address->setData('novaposhta_street_ref', $extensionAttributes->getNovaposhtaStreetRef() ?? $address->getData('novaposhta_street_ref'));
        } catch (\Exception $e) {
        }

        return [$cartId, $address];
    }
}
