<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Model\Integration;

use Amasty\ShopbyBase\Model\Integration\IntegrationException;

class DummyObject
{
    /**
     * @param string $method
     * @param array $args
     * @return null
     * @throws IntegrationException
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) === 'get') {
            return null;
        }

        throw new IntegrationException();
    }
}
