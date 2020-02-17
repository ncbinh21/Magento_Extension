<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Api;

interface GroupRepositoryInterface
{
    /**
     * @param $groupId
     * @return false or array
     */
    public function getGroupOptionsIds($groupId);
}
