<?php

namespace Yu\NovaPoshta\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\ResourceModel\Order\Address\Collection;

class OrderAddressCollectionLoadAfter implements ObserverInterface
{

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    private $quoteAddressFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Address
     */
    private $quoteAddressResource;

    /**
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address $quoteAddressResource
     */
    public function __construct(
        \Magento\Quote\Model\Quote\AddressFactory        $quoteAddressFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Address $quoteAddressResource
    ) {
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->quoteAddressResource = $quoteAddressResource;
    }

    public function execute(Observer $observer)
    {
        /** @var Collection $collection */
        $collection = $observer->getData('order_address_collection');

        /** @var Address $item */
        foreach ($collection as $item) {
            $quoteAddressId = $item->getData('quote_address_id');

            if ($item->getAddressType() !== 'shipping' || !$quoteAddressId) {
                continue;
            }

            /** @var \Magento\Quote\Model\Quote\Address $quoteAddress */
            $quoteAddress = $this->quoteAddressFactory->create();
            $this->quoteAddressResource->load($quoteAddress, $quoteAddressId);

            $extensionAttributes = $item->getExtensionAttributes();

            $extensionAttributes->setNovaposhtaCityRef($quoteAddress->getData('novaposhta_city_ref'));
            $extensionAttributes->setNovaposhtaWarehouseRef($quoteAddress->getData('novaposhta_warehouse_ref'));
            $extensionAttributes->setNovaposhtaStreetRef($quoteAddress->getData('novaposhta_street_ref'));

            $item->setExtensionAttributes($extensionAttributes);
        }
    }
}
