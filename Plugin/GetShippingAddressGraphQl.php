<?php

namespace Yu\NovaPoshta\Plugin;

use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\QuoteGraphQl\Model\Cart\GetShippingAddress;

class GetShippingAddressGraphQl
{

    public function afterExecute(
        GetShippingAddress $subject,
        Address            $shippingAddress,
        ContextInterface   $context,
        array              $shippingAddressInput
    ) {
        $addressInput = $shippingAddressInput['address'] ?? null;
        $extensionAttributes = $shippingAddress->getExtensionAttributes();

        if (!array_key_exists('perspective_np', $addressInput)) {
            return $shippingAddress;
        }

        $dataArray = $addressInput['perspective_np'];

        if (array_key_exists('city_ref', $dataArray)) {
            $extensionAttributes->setNovaposhtaCityRef($addressInput['perspective_np']['city_ref']);
        }

        if (array_key_exists('warehouse_ref', $dataArray)) {
            $extensionAttributes->setNovaposhtaWarehouseRef($addressInput['perspective_np']['warehouse_ref']);
        }

        if (array_key_exists('street_ref', $dataArray)) {
            $extensionAttributes->setNovaposhtaStreetRef($addressInput['perspective_np']['street_ref']);
        }

        $shippingAddress->setExtensionAttributes($extensionAttributes);
        return $shippingAddress;
    }
}
