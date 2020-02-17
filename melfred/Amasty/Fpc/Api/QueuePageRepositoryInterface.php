<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */

namespace Amasty\Fpc\Api;

use Amasty\Fpc\Api\Data\QueuePageInterface;

interface QueuePageRepositoryInterface
{
    public function delete(QueuePageInterface $entity);

    public function save(QueuePageInterface $entity);

    /**
     * @param array $pageData
     *
     * @return \Amasty\Fpc\Model\Queue\Page mixed
     */
    public function addPage($pageData);

    public function clear();
}
