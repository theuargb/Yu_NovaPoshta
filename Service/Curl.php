<?php

namespace Yu\NovaPoshta\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Сервисный класс для синхронизации данных с novaposhta.ua
 */
class Curl
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\Client\Curl                $curl
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curl;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getDataImport($params)
    {
        $apiKey = $this->scopeConfig->getValue(
            'carriers/novaposhta/api_key',
            ScopeInterface::SCOPE_STORE
        );

        $params['apiKey'] = $apiKey;

        $this->curl->setHeaders(["content-type: application/json"]);
        $this->curl->post("https://api.novaposhta.ua/v2.0/json/", json_encode($params));

        $json = $this->curl->getBody();
        $data = json_decode($json, true)['data'];

        return $data;
    }
}
