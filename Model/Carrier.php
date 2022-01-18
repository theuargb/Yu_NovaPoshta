<?php

namespace Yu\NovaPoshta\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Shipping\Model\Tracking\Result\Status;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Yu\NovaPoshta\Service\Curl;

/**
 * NovaPoshta shipping model
 */
class Carrier extends AbstractCarrier implements CarrierInterface
{

    const METHOD_WAREHOUSE = 'novaposhta_to_warehouse';
    const METHOD_DOOR = 'novaposhta_to_door';

    /**
     * @var string
     */
    protected $_code = 'novaposhta';

    /**
     * @var bool
     */
    protected $_isFixed = false;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var StatusFactory
     */
    protected $trackResultFactory;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var \Yu\NovaPoshta\Model\ResourceModel\City
     */
    private $cityResourceModel;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param StatusFactory $trackResultFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface          $scopeConfig,
        ErrorFactory  $rateErrorFactory,
        LoggerInterface                                    $logger,
        ResultFactory                  $rateResultFactory,
        MethodFactory $rateMethodFactory,
        StatusFactory       $trackResultFactory,
        Session                             $checkoutSession,
        Curl                                 $curl,
        \Yu\NovaPoshta\Model\ResourceModel\City                     $cityResourceModel,
        array                                                       $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->trackResultFactory = $trackResultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->cityResourceModel = $cityResourceModel;
        $this->curl = $curl;
    }

    /**
     * NovaPoshta Rates Collector
     *
     * @param RateRequest $request
     *
     * @return Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $cityRef = '';

        $cityName = '';
        //print_r($request->getData());die;
        if ($request->getOrigCity()) {
            $cityName = $request->getOrigCity();
        } else if ($request->getDestCity()) {
            $cityName = $request->getDestCity();
        }

        $cityRef = $this->cityResourceModel->getRefByName($cityName);

        /* crutch */
        if (empty($cityRef)) {
            if ($this->checkoutSession->getQuote()->getShippingAddress()->getCityNovaposhtaRef()) {
                $cityRef = $this->checkoutSession->getQuote()->getShippingAddress()->getCityNovaposhtaRef();
            }
        }


        $citySender = $this->_scopeConfig->getValue(
            'carriers/novaposhta/city_sender',
            ScopeInterface::SCOPE_STORE
        );

        $carrierTitle = $this->_scopeConfig->getValue(
            'carriers/novaposhta/title',
            ScopeInterface::SCOPE_STORE
        );

        $nameWW = $this->_scopeConfig->getValue(
            'carriers/novaposhta/name_ww',
            ScopeInterface::SCOPE_STORE
        );

        $nameWD = $this->_scopeConfig->getValue(
            'carriers/novaposhta/name_wd',
            ScopeInterface::SCOPE_STORE
        );

        $allowed = $this->getAllowedMethods();

        /** @var Result $result */
        $result = $this->rateResultFactory->create();


        /** WarehouseWarehouse  */
        if (in_array(self::METHOD_WAREHOUSE, $allowed)) {

            $customPrice = $this->getCustomPrice('warehouse');

            $params = [
                'modelName' => 'InternetDocument',
                'calledMethod' => 'getDocumentPrice',
                'apiKey' => '',
                'methodProperties' => [
                    'CitySender' => $citySender,
                    'CityRecipient' => $cityRef,
                    'Weight' => $request->getPackageWeight(),
                    'ServiceType' => 'WarehouseWarehouse',
                    'Cost' => $this->checkoutSession->getQuote()->getBaseSubtotal(),
                    'CargoType' => 'Cargo',
                    'SeatsAmount' => 1,
                ]];

            $price = 0;
            if ($customPrice !== null) {
                $price = $customPrice;
            } else {
                $price = $this->getNovaPoshtaPrice($params);
            }

            /** @var Method $methodWarehouse */
            $methodWarehouse = $this->rateMethodFactory->create();
            $methodWarehouse->setCarrier($this->_code);
            $methodWarehouse->setCarrierTitle($carrierTitle);
            $methodWarehouse->setMethod('novaposhta_to_warehouse');
            $methodWarehouse->setMethodTitle($nameWW);
            $methodWarehouse->setPrice($price);

            $result->append($methodWarehouse);
        }


        /** WarehouseDoors  */
        if (in_array(self::METHOD_DOOR, $allowed)) {

            $customPrice = $this->getCustomPrice('door');

            $params = [
                'modelName' => 'InternetDocument',
                'calledMethod' => 'getDocumentPrice',
                'apiKey' => '',
                'methodProperties' => [
                    'CitySender' => $citySender,
                    'CityRecipient' => $cityRef,
                    'Weight' => $request->getPackageWeight(),
                    'ServiceType' => 'WarehouseDoors',
                    'Cost' => $this->checkoutSession->getQuote()->getBaseSubtotal(),
                    'CargoType' => 'Cargo',
                    'SeatsAmount' => 1,
                ]];

            $price = 0;
            if ($customPrice !== null) {
                $price = $customPrice;
            } else {
                $price = $this->getNovaPoshtaPrice($params);
            }

            /** @var Method $methodDoor */
            $methodDoor = $this->rateMethodFactory->create();
            $methodDoor->setCarrier($this->_code);
            $methodDoor->setCarrierTitle($carrierTitle);
            $methodDoor->setMethod('novaposhta_to_door');
            $methodDoor->setMethodTitle($nameWD);
            $methodDoor->setPrice($price);

            $result->append($methodDoor);
        }

        return $result;
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     *
     * @return array|false
     */
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                self::METHOD_WAREHOUSE => __('До склада'),
                self::METHOD_DOOR => __('До дверей'),
            ],
        ];

        if (!isset($codes[$type])) {
            return false;
        } else if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = [];
        /**
         * foreach ($allowed as $_code)
         * {
         * $arr[$_code] = $this->getCode('method', $_code);
         * } */
        return $allowed;
    }

    /**
     * Get custom price from system config.
     * $type = 'warehouse' or 'door'
     *
     * @param string $type
     *
     * @return float|null
     */
    public function getCustomPrice($type)
    {
        $price = -1;

        /** fix price */
        if (!empty($this->getConfigData('price_fix_' . $type))) {
            $price = $this->getConfigData('price_fix_' . $type);
        }

        /** free amount */
        if (!empty($this->getConfigData('amount_free_' . $type))) {

            $amountFree = (float)$this->getConfigData('amount_free_' . $type);
            if ($amountFree <= $this->checkoutSession->getQuote()->getGrandTotal()) {
                $price = 0;
            }
        }

        if ($price !== -1) {
            return $price;
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @return float|null
     */
    public function getNovaPoshtaPrice($params)
    {
        if (empty($params['methodProperties']['Weight'])) {
            $params['methodProperties']['Weight'] = 1;
        }

        $data = $this->curl->getDataImport($params);

        $cost = '';
        if (isset($data[0]['Cost'])) {
            $cost = $data[0]['Cost'];
        }

        return $cost;
    }

    /**
     * Check if tracking info is available
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Get tracking information
     *
     * @param string $trackNumber
     *
     * @return Status
     * @api
     */
    public function getTrackingInfo($trackNumber)
    {
        $title = $this->getConfigData('title');
        $url = 'https://novaposhta.ua/tracking/index/cargo_number/' . $trackNumber . '/no_redirect/1';
        return $this->trackResultFactory->create()->setCarrierTitle($title)->setTracking($trackNumber)->setUrl($url);
    }

}
