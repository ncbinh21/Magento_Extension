<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: SockDreams
 */
namespace Forix\AdvancedPaypal\Rewrite\Magento\Paypal\Model\Payflow\Service;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Framework\DataObject;
class Gateway extends \Magento\Paypal\Model\Payflow\Service\Gateway {
    public function postRequest(DataObject $request, ConfigInterface $config)
    {
        $requestData = array();
        foreach($request->getData() as $k => $v){
            if (mb_stripos($v, '&') !== false || mb_stripos($v, '=') !== false) {
                $k .= '[' . mb_strlen($v) . ']';
            }
            $requestData[$k] = $v;
        }


        $result = new DataObject();

        $clientConfig = [
            'maxredirects' => 5,
            'timeout' => 30,
            'verifypeer' => $config->getValue('verify_peer')
        ];

        if ($config->getValue('use_proxy')) {
            $clientConfig['proxy'] = $config->getValue('proxy_host')
                . ':'
                . $config->getValue('proxy_port');
            $clientConfig['httpproxytunnel'] = true;
            $clientConfig['proxytype'] = CURLPROXY_HTTP;
        }

        /** @var ZendClient $client */
        $client = $this->httpClientFactory->create();

        $client->setUri(
            (bool)$config->getValue('sandbox_flag')
                ? $config->getValue('transaction_url_test_mode')
                : $config->getValue('transaction_url')
        );
        $client->setConfig($clientConfig);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setParameterPost($requestData);
        $client->setHeaders(
            [
                'X-VPS-VIT-CLIENT-CERTIFICATION-ID' => '33baf5893fc2123d8b191d2d011b7fdc',
                'X-VPS-Request-ID' => $this->mathRandom->getUniqueHash(),
                'X-VPS-CLIENT-TIMEOUT' => 45
            ]
        );
        $client->setUrlEncodeBody(false);

        try {
            $response = $client->request();

            $responseArray = [];
            parse_str(strstr($response->getBody(), 'RESULT'), $responseArray);

            $result->setData(array_change_key_case($responseArray, CASE_LOWER));
            $result->setData('result_code', $result->getData('result'));

        } catch (\Exception $e) {
            $result->addData(
                [
                    'response_code' => -1,
                    'response_reason_code' => $e->getCode(),
                    'response_reason_text' => $e->getMessage()
                ]
            );

            throw $e;
        } finally {
            $this->logger->debug(
                [
                    'request' => $requestData,
                    'result' => $result->getData()
                ],
                (array)$config->getValue('getDebugReplacePrivateDataKeys'),
                (bool)$config->getValue('debug')
            );
        }

        return $result;
    }
}