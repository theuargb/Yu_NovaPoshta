<?php

namespace Yu\NovaPoshta\Plugin;


use Magento\Framework\Webapi\Rest\Request;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;

class NovaPoshtaCheckPlugin
{
    private $request;

    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * NovaPoshtaCheckPlugin constructor.
     *
     * @param AddressInterface $address
     */
    public function __construct(
        Request   $request,
        AddressInterface $address
    ) {
        $this->request = $request;
        $this->address = $address;
    }

    public function afterEstimateByExtendedAddress(
        ShipmentEstimationInterface $subject,
                                                       $result,
                                                       $cartId,
                                                       $addressId
    ) {
        $novaposhtaCheck = 0;
        $methods = [];
        /** @var ShippingMethodInterface $item */
        $params = $this->request->getRequestData();
        if (isset($params['address']['custom_attributes'])) {
            foreach ($params['address']['custom_attributes'] as $param) {
                if ($param['attribute_code'] == 'novaposhta_check' && !empty($param['value'])) {
                    $novaposhtaCheck = 1;
                }
            }
        }

        foreach ($result as $item) {
            if ($item->getCarrierCode() == 'novaposhta' && $novaposhtaCheck == 1) {
                $methods[] = $item;
            }

            if ($item->getCarrierCode() !== 'novaposhta' && $novaposhtaCheck == 0) {
                $methods[] = $item;
            }
        }

        return $methods;
    }
}
