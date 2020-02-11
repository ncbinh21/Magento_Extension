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



namespace Mirasvit\Email\Cron;

use Magento\Framework\Event\Observer;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;

class SendQueue
{
    /**
     * @var QueueRepositoryInterface
     */
    protected $collectionRepository;

    /**
     * Constructor
     *
     * @param QueueRepositoryInterface $collectionRepository
     */
    public function __construct(
        QueueRepositoryInterface $collectionRepository
    ) {
        $this->collectionRepository = $collectionRepository;
    }

    /**
     * Send emails from queue.
     *
     * @return void
     */
    public function execute()
    {
        $collection = $this->collectionRepository->getCollection();
        $collection->addReadyFilter();

        foreach ($collection as $item) {
            try {
                $item->send();
            } catch (\Exception $e) {
                $item->error($e->__toString());
            }
        }
    }
}
