<?php

namespace Yu\NovaPoshta\Plugin;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Api\Data\OrderAddressInterface;

class QuoteToOrderAddressConvert
{

    public function afterConvert(ToOrderAddress $subject, OrderAddressInterface $result, Address $object, $data = [])
    {
        $result->setCityNovaposhtaRef($result->getCityNovaposthtaRef());

        return $result;
    }
}
