<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source;

use Amasty\Fpc\Model\Config;

class All implements SourceInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var PageType\Factory
     */
    private $pageTypeFactory;

    public function __construct(
        Config $config,
        PageType\Factory $pageTypeFactory
    ) {
        $this->config = $config;
        $this->pageTypeFactory = $pageTypeFactory;
    }

    /**
     * Return pages to crawl
     *
     * @param int    $queueLimit
     * @param string $eMessage
     *
     * @return array
     */
    public function getPages($queueLimit, $eMessage)
    {
        $result = [];

        $config = $this->config->getPagesConfig();

        $queueLimit = $this->config->getValue('crawler/max_queue_size');

        uasort($config, function ($a, $b) {
            return $a['priority'] < $b['priority'] ? -1 : 1;
        });

        foreach ($config as $typeCode => $params) {
            if (!isset($params['enabled']) || !$params['enabled']) {
                continue;
            }

            $pageType = $this->pageTypeFactory->create($typeCode);

            $pages = $pageType->getAllPages($queueLimit - count($result));

            $rate = count($config) - $params['priority'] + 1;
            $pages = array_map(function ($page) use ($rate) {
                $page['rate'] = $rate;
                return $page;
            }, $pages);

            $result = array_merge($result, $pages);

            if (count($result) >= $queueLimit) {
                break;
            }
        }

        return $result;
    }
}
