<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

class PageStatus
{
    const STATUS_UNDEFINED = 'undefined';
    const STATUS_HIT = 'hit';
    const STATUS_MISS = 'miss';
    const STATUS_IGNORED = 'ignored';

    protected $status = self::STATUS_HIT;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
