<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Api\Data;

interface QueuePageInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const URL = 'url';
    const RATE = 'rate';
    const STORE = 'store';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\Fpc\Api\Data\QueuePageInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     *
     * @return \Amasty\Fpc\Api\Data\QueuePageInterface
     */
    public function setUrl($url);

    /**
     * @return int
     */
    public function getRate();

    /**
     * @param int $rate
     *
     * @return \Amasty\Fpc\Api\Data\QueuePageInterface
     */
    public function setRate($rate);

    /**
     * @return int|null
     */
    public function getStore();

    /**
     * @param int|null $store
     *
     * @return \Amasty\Fpc\Api\Data\QueuePageInterface
     */
    public function setStore($store);
}
