<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

use Magento\Framework\App\Area;

abstract class Emulated extends AbstractPage
{
    /**
     * @var \Magento\Framework\Url
     */
    private $url;
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;
    /**
     * @var \Closure
     */
    private $filterCollection;

    public function __construct(
        \Magento\Framework\Url $url,
        \Magento\Store\Model\App\Emulation $appEmulation,
        $isMultistoreMode = false,
        array $stores = [],
        \Closure $filterCollection = null
    ) {
        parent::__construct($isMultistoreMode, $stores);

        $this->url = $url;
        $this->appEmulation = $appEmulation;
        $this->filterCollection = $filterCollection;
    }

    abstract protected function getEntityCollection($storeId);
    abstract protected function getUrl($entity, $storeId);

    public function getAllPages($limit = 0)
    {
        $result = [];

        $object = new \stdClass();

        foreach ($this->stores as $storeId) {
            // Clear base url cache
            $this->url->unsetData('scope');
            $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND);

            $collection = $this->getEntityCollection($storeId);

            if (is_callable($this->filterCollection)) {
                $callback = $this->filterCollection;
                $callback($collection);
            }

            foreach ($collection as $entity) {
                $url = $this->getUrl($entity, $storeId);

                $result [] = [
                    'url' => $this->url->getUrl(null, [
                        '_nosid' => true,
                        'object' => $object, // Pass object to params to prevent url caching
                        '_direct' => $url
                    ]),
                    'store' => $storeId
                ];

                if (--$limit == 0) {
                    break 2;
                }
            }
            $this->appEmulation->stopEnvironmentEmulation();
        }
        $this->appEmulation->stopEnvironmentEmulation();

        return $result;
    }
}
