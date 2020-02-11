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



namespace Mirasvit\Email\Service\EmailReport;


use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\EmailReport\Api\Service\ParamProviderInterface;
use Mirasvit\EmailReport\Api\Service\StorageServiceInterface;

class QueueParamProvider implements ParamProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getParams(QueueInterface $queue)
    {
        $params = [];
        if ($queue->getId()) {
            $params[] = StorageServiceInterface::QUEUE_PARAM_NAME.'='.$queue->getUniqHash();
        }

        return $params;
    }
}
