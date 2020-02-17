<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Product\Model\Indexer;

class GroundCondition implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface

{

    protected $groundIndex;

    public function __construct(
        \Forix\Product\Model\GroundIndex $groundIndex
    )
    {
        $this->groundIndex = $groundIndex;
    }


    public function execute($ids)
    {
    }

    public function executeFull()
    {
        $this->groundIndex->rebuiltIndex();
    }


    public function executeList(array $ids)
    {
//        file_put_contents('/home/kuton/Desktop/log.txt', "dada4", FILE_APPEND);
    }


    public function executeRow($id)
    {
    }


    protected function getCacheContext()
    {
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }

}




