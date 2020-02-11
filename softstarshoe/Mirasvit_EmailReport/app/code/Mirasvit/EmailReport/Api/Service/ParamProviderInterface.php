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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Api\Service;


use Mirasvit\Email\Api\Data\QueueInterface;

interface ParamProviderInterface
{
    /**
     * Method provides params for GET request.
     *
     * - param_name=param_value
     * - param_name2=param_value2
     *
     * @param QueueInterface $queue
     *
     * @return \string[]
     */
    public function getParams(QueueInterface $queue);
}