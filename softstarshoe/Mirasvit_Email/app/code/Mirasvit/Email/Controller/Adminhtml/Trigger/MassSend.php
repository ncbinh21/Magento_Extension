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



namespace Mirasvit\Email\Controller\Adminhtml\Trigger;

use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Email\Controller\Adminhtml\Trigger;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;

class MassSend extends Trigger
{
    /**
     * @var Filter
     */
    private $filter;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter,
        Filter $filter,
        TriggerRepositoryInterface $triggerRepository,
        Registry $registry,
        Context $context
    ) {
        $this->filter = $filter;

        parent::__construct($dateTimeFilter, $triggerRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('advn_action_field');
        $collection = $this->filter->getCollection($this->triggerRepository->getCollection());

        if (!$collection->getSize()) {
            $this->messageManager->addErrorMessage(__('Please select trigger(s)'));
        } else {
            try {
                foreach ($collection as $trigger) {
                    $trigger->sendTest($email);
                }

                $this->messageManager->addSuccessMessage(__('Total of %1 email(s) were sent', $collection->getSize()));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
