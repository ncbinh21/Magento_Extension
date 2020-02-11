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

class GaParamProvider implements ParamProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getParams(QueueInterface $queue)
    {
        $params = [];
        $trigger = $queue->getTrigger();
        if ($trigger->getId() && $trigger->getGaSource() && $trigger->getGaMedium() && $trigger->getGaName()) {
            $params[] = 'utm_source=' . rawurlencode($trigger->getGaSource());
            $params[] = 'utm_medium=' . rawurlencode($trigger->getGaMedium());
            $params[] = 'utm_campaign=' . rawurlencode($trigger->getGaName());
            if ($trigger->getGaTerm() != '') {
                $params[] = 'utm_term=' . rawurlencode($trigger->getGaTerm());
            }
            if ($trigger->getGaContent() != '') {
                $params[] = 'utm_content=' . rawurlencode($trigger->getGaContent());
            }
        }

        return $params;
    }
}
