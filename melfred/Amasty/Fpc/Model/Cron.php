<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Exception\LockException;
use Psr\Log\LoggerInterface;

class Cron
{
    /**
     * @var Queue
     */
    private $queue;
    /**
     * @var \Amasty\Fpc\Model\Config
     */
    private $config;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Queue $queue,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->queue = $queue;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function generate()
    {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        try {
            $this->queue->generate();
        } catch (LockException $e) {
            $this->logger->info(__('Can\'t get a file lock for queue generation process $1', $e->getMessage()));
        }
    }

    public function process()
    {
        if (!$this->config->isModuleEnabled()) {
            return;
        }

        try {
            $this->queue->process();
        } catch (LockException $e) {
            $this->logger->info(__('Can\'t get a file lock for queue processing process $1', $e->getMessage()));
        }
    }
}
