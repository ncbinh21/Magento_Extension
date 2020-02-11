<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Api\Repository\Trigger;

use Mirasvit\Email\Api\Data\TriggerChainInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;

interface ChainRepositoryInterface
{
    /**
     * @return TriggerChainInterface[]|\Mirasvit\Email\Model\ResourceModel\Trigger\Chain\Collection
     */
    public function getCollection();

    /**
     * @return TriggerChainInterface
     */
    public function create();

    /**
     * @param int $id
     * @return TriggerChainInterface|false
     */
    public function get($id);

    /**
     * @param TriggerChainInterface $model
     * @return TriggerChainInterface
     */
    public function save(TriggerChainInterface $model);

    /**
     * @param TriggerChainInterface $model
     * @return bool
     */
    public function delete(TriggerChainInterface $model);
}