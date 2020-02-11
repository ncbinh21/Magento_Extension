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


namespace Mirasvit\Email\Console\Command;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mirasvit\Email\Cron\CleanHistory;
use Mirasvit\Email\Cron\HandleEvents;
use Mirasvit\Email\Cron\SendQueue;

class CronCommand extends Command
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @var CleanHistory
     */
    protected $cleanHistory;

    /**
     * @var HandleEvents
     */
    protected $fetchEvents;

    /**
     * @var SendQueue
     */
    protected $sendQueue;

    /**
     * @param State        $state
     * @param CleanHistory $cleanHistory
     * @param HandleEvents  $fetchEvents
     * @param SendQueue    $sendQueue
     */
    public function __construct(
        State $state,
        CleanHistory $cleanHistory,
        HandleEvents $fetchEvents,
        SendQueue $sendQueue
    ) {
        $this->state = $state;
        $this->cleanHistory = $cleanHistory;
        $this->fetchEvents = $fetchEvents;
        $this->sendQueue = $sendQueue;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:email:cron')
            ->setDescription('Run cron jobs')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('frontend');

        $output->write('Cron "Fetch Events"....');
        $this->fetchEvents->execute();
        $output->writeln('done');

        $output->write('Cron "Send Queue"....');
        $this->sendQueue->execute();
        $output->writeln('done');

        $output->write('Cron "Clean History"....');
        $this->cleanHistory->execute();
        $output->writeln('done');
    }
}
