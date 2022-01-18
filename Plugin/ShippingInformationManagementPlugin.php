<?php

namespace Yu\NovaPoshta\Plugin;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class ShippingInformationManagementPlugin
{

    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement   $subject,
                                                                $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        $extensionAttributes = $shippingAddress->getExtensionAttributes();
        $shippingAddress->setCityNovaposhtaRef($extensionAttributes->getCityNovaposhtaRef());
        $shippingAddress->setWarehouseNovaposhtaId($extensionAttributes->getWarehouseNovaposhtaId());
        $shippingAddress->setWarehouseNovaposhtaAddress($extensionAttributes->getWarehouseNovaposhtaAddress());
    }

    /**
     *
     * @param ShippingInformationManagement $subject
     * @param PaymentDetailsInterface $result
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @return PaymentDetailsInterface
     */
    public function afterSaveAddressInformation(
        ShippingInformationManagement   $subject,
                                                                $result,
                                                                $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $shippingAddress = $quote->getShippingAddress();

        if ($addressInformation->getShippingMethodCode() == 'novaposhta_to_warehouse') {
            $shippingAddress->setStreet($shippingAddress->getWarehouseNovaposhtaAddress());
            $shippingAddress->save();
        }
        return $result;
    }
}
