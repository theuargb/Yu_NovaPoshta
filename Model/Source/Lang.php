<?php

namespace Yu\NovaPoshta\Model\Source;

/**
 * Lang source
 */
class Lang implements \Magento\Shipping\Model\Carrier\Source\GenericInterface
{

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 'ua', 'label' => 'Українська'],
            ['value' => 'ru', 'label' => 'Російяська'],
        ];
        
        return $options;
    }

}
