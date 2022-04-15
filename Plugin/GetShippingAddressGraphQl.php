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

        if (array_key_exists('perspective_np', $addressInput)) {
            $extensionAttributes->setCityNovaposhtaRef($addressInput['perspective_np']['city_ref']);
            $extensionAttributes->setWarehouseNovaposhtaRef($addressInput['perspective_np']['warehouse_ref']);
            $extensionAttributes->setStreetNovaposhtaRef($addressInput['perspective_np']['city_ref']);
        }

        $shippingAddress->setExtensionAttributes($extensionAttributes);
        return $shippingAddress;
    }
}
