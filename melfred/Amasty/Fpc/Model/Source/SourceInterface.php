<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Source;

interface SourceInterface
{
    /**
     * @param int    $queueLimit
     * @param string $eMessage
     *
     * @return array
     */
    public function getPages($queueLimit, $eMessage);
}
