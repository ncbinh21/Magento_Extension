<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 23/10/2017
 * Time: 10:35
 */

namespace Magenest\SagepayUS\Helper;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $fileName = '/var/log/sagepayus/debug.log';
    protected $loggerType = \Monolog\Logger::DEBUG;
}
