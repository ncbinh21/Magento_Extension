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

use Mirasvit\Mq\Api\Data\EnvelopeInterfaceFactory;
use Mirasvit\Mq\Api\Data\EnvelopeInterface;
use Mirasvit\Mq\Api\QueueInterface;
use PhpAmqpLib\Message\AMQPMessage;

class Queue implements QueueInterface
{
    const QUEUE_NAME = 'mq';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EnvelopeInterfaceFactory
     */
    private $envelopeFactory;

    public function __construct(
        Client $client,
        EnvelopeInterfaceFactory $envelopeFactory
    ) {
        $this->client = $client;
        $this->envelopeFactory = $envelopeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        try {
            $this->client->getChannel();
            $this->ensureQueue();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function enqueue(EnvelopeInterface $envelope)
    {
        $channel = $this->client->getChannel();

        $message = new AMQPMessage(
            $envelope->getBody(), [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'type'          => $envelope->getQueueName(),
            ]
        );

        $channel->basic_publish($message, self::QUEUE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function peek()
    {
        $channel = $this->client->getChannel();

        /** @var AMQPMessage $message */
        $message = $channel->basic_get(self::QUEUE_NAME);

        if (!$message) {
            return false;
        }

        $envelope = $this->envelopeFactory->create();
        $envelope->setReference($message->delivery_info['delivery_tag'])
            ->setQueueName($message->get_properties()['type'])
            ->setBody($message->body);

        return $envelope;
    }

    /**
     * {@inheritdoc}
     */
    public function acknowledge(EnvelopeInterface $envelope)
    {
        $this->client->getChannel()
            ->basic_ack($envelope->getReference());
    }

    /**
     * {@inheritdoc}
     */
    public function reject(EnvelopeInterface $envelope, $requeue = false)
    {
        $this->client->getChannel()
            ->basic_reject($envelope->getReference(), $requeue);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe($callback, $maxExecutionTime)
    {
        $ts = microtime(true);

        while (true) {
            while ($envelope = $this->peek()) {
                try {
                    call_user_func($callback, $envelope);
                    $this->acknowledge($envelope);
                } catch (\Exception $e) {
                    echo $e;
                    $this->reject($envelope);
                }

                if (microtime(true) - $ts > $maxExecutionTime) {
                    break;
                }
            }

            sleep(1);

            if (microtime(true) - $ts > $maxExecutionTime) {
                break;
            }
        }
    }

    /**
     * @return void
     */
    private function ensureQueue()
    {
        $channel = $this->client->getChannel();

        $channel->queue_declare(self::QUEUE_NAME, false, true, false, false);

        $channel->exchange_declare(self::QUEUE_NAME, 'direct', false, true, false);

        $channel->queue_bind(self::QUEUE_NAME, self::QUEUE_NAME);
    }
}