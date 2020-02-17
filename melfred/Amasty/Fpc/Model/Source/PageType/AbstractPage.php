<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source\PageType;

abstract class AbstractPage
{
    protected $isMultistoreMode;
    protected $stores;

    public function __construct(
        $isMultistoreMode = false,
        $stores = []
    ) {
        $this->isMultistoreMode = $isMultistoreMode;
        $this->stores = $stores;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    abstract public function getAllPages($limit = 0);
}
