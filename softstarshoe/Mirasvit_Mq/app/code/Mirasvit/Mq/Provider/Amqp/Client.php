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
 * @package   mirasvit/module-message-queue
 * @version   1.0.2
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Mq\Provider\Amqp;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Exception\ConfigurationMismatchException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Client
{
    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    public function __construct(
        DeploymentConfig $deploymentConfig
    ) {
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @return AMQPChannel
     * @throws ConfigurationMismatchException
     */
    public function getChannel()
    {
        if (!$this->channel && $this->deploymentConfig->get('queue/amqp/host')) {
            $connection = new AMQPStreamConnection(
                $this->deploymentConfig->get('queue/amqp/host'),
                $this->deploymentConfig->get('queue/amqp/port'),
                $this->deploymentConfig->get('queue/amqp/username'),
                $this->deploymentConfig->get('queue/amqp/password'),
                $this->deploymentConfig->get('queue/amqp/virtual_host')
            );

            $this->channel = $connection->channel();
        } elseif (!$this->deploymentConfig->get('queue/amqp/host')) {
            throw new ConfigurationMismatchException(__('Host field required to set up connection with RabbitMQ'));
        }

        return $this->channel;
    }
}