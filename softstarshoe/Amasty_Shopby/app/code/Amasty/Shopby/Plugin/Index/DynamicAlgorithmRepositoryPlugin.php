<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Index;

use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Dynamic\Algorithm;

/**
 * For temporary use. Untill all buckets will be evaluated in 1 request @todo
 *
 * Class DynamicAlgorithmRepositoryPlugin
 * @package Amasty\Shopby\Plugin
 */
class DynamicAlgorithmRepositoryPlugin
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Reinitialize shared instance. In order to get correct aggregations for a price when it has current value applied
     *
     * @param Repository $subject
     * @param \Closure $closure
     * @param $algorithmType
     * @param array $data
     * @return mixed
     */
    public function aroundGet(Repository $subject, \Closure $closure, $algorithmType, array $data = [])
    {
        $result = $closure($algorithmType, $data);
        if ($algorithmType == 'auto') {
            return $this->objectManager->create(Algorithm\Auto::class, $data);
        } elseif ($algorithmType == 'manual') {
            return $this->objectManager->create(Algorithm\Manual::class, $data);
        }

        return $result;
    }
}
