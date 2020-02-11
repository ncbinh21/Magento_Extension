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



namespace Mirasvit\Email\Controller\Adminhtml\Event;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Email\Controller\Adminhtml\Event;
use Mirasvit\Email\Cron\HandleEvents;
use Mirasvit\Event\Api\Repository\EventRepositoryInterface;

class Fetch extends Event
{
    /**
     * @var HandleEvents
     */
    protected $handleEvents;

    public function __construct(
        HandleEvents $handleEvents,
        EventRepositoryInterface $eventRepository,
        Registry $registry,
        Context $context
    ) {
        $this->handleEvents = $handleEvents;

        parent::__construct($eventRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $this->handleEvents->execute(); // Register events and process them with triggers

        $this->messageManager->addSuccessMessage(__('Completed.'));

        return $resultRedirect->setPath('*/*');
    }
}
