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



namespace Mirasvit\Mq\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mirasvit\Mq\Api\Service\QueueServiceInterface;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Filesystem\LockHandler;

class SubscribeCommand extends Command
{
    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var QueueServiceInterface
     */
    private $queueService;

    /**
     * @var DirectoryList
     */
    private $directory;

    public function __construct(
        AppState $appState,
        QueueServiceInterface $queueService,
        DirectoryList $directory
    ) {
        $this->appState = $appState;
        $this->queueService = $queueService;
        $this->directory = $directory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:message-queue:subscribe')
            ->setDescription('Subscribe')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('global');
        } catch (\Exception $e) {
            # already set by another module
        }

        $locker = new LockHandler('mq', $this->directory->getPath(DirectoryList::TMP));
        if ($locker->lock()) {
            $this->queueService->subscribe(10 * 60);
            $locker->release();
        }
    }
}
