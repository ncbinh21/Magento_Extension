<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */

namespace Amasty\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use SimpleXMLElement;
use Zend\Http\Client\Adapter\Curl as CurlClient;
use Zend\Http\Response as HttpResponse;
use Zend\Uri\Http as HttpUri;

class Module extends AbstractHelper
{
    const EXTENSIONS_PATH = 'ambase_extensions';
    const URL_EXTENSIONS  = 'http://amasty.com/feed-extensions-m2.xml';

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;
    /**
     * @var CurlClient
     */
    protected $curlClient;
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $restrictedModules = [
        'Amasty_CommonRules'
    ];

    /**
     * Module constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Amasty\Base\Model\Serializer $serializer
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param CurlClient $curl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Amasty\Base\Model\Serializer $serializer,
        \Magento\Framework\App\CacheInterface $cache,
        CurlClient $curl
    ) {
        parent::__construct($context);

        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->curlClient = $curl;
    }

    /**
     * Get array with info about all Amasty Magento2 Extensions
     * @return bool|mixed
     */
    public function getAllExtensions()
    {
        $serialized = $this->cache->load(self::EXTENSIONS_PATH);
        if ($serialized === false) {
            $this->reload();
            $serialized = $this->cache->load(self::EXTENSIONS_PATH);
        }
        $result = $this->serializer->unserialize($serialized);

        return $result;
    }

    /**
     * Save extensions data to magento cache
     */
    protected function reload()
    {
        $feedData = [];
        $feedXml = $this->getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $code = (string)$item->code;

                if (!isset($feedData[$code])) {
                    $feedData[$code] = [];
                }

                $feedData[$code][(string)$item->title] = [
                    'name'    => (string)$item->title,
                    'url'     => (string)$item->link,
                    'version' => (string)$item->version,
                ];
            }

            if ($feedData) {
                $this->cache->save($this->serialize($feedData), self::EXTENSIONS_PATH);
            }
        }
    }

    /**
     * Read data from xml file with curl
     * @return bool|SimpleXMLElement
     */
    protected function getFeedData()
    {
        try {
            $curlClient = $this->getCurlClient();

            $location = self::URL_EXTENSIONS;
            $uri = new HttpUri($location);

            $curlClient->setOptions([
                'timeout'   => 8
            ]);

            $curlClient->connect($uri->getHost(), $uri->getPort());
            $curlClient->write('GET', $uri, 1.0);
            $data = HttpResponse::fromString($curlClient->read());

            $curlClient->close();

            $xml  = new SimpleXMLElement($data->getContent());
        } catch (\Exception $e) {
            return false;
        }

        return $xml;
    }

    /**
     * Returns the cURL client that is being used.
     *
     * @return CurlClient
     */
    public function getCurlClient()
    {
        if ($this->curlClient === null) {
            $this->curlClient = new CurlClient();
        }
        return $this->curlClient;
    }

    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }

    /**
     * @return array
     */
    public function getRestrictedModules()
    {
        return $this->restrictedModules;
    }
}
