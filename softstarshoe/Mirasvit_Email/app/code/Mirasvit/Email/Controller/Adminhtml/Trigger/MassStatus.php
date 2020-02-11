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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;
use Mirasvit\Email\Controller\Adminhtml\Trigger;

class MassStatus extends Trigger
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
        $status = $this->getRequest()->getParam(TriggerInterface::IS_ACTIVE, TriggerInterface::STATUS_DISABLED);
        $collection = $this->filter->getCollection($this->triggerRepository->getCollection());

        if (!$collection->getSize()) {
            $this->messageManager->addErrorMessage(__('Please select trigger(s)'));
        } else {
            try {
                foreach ($collection as $trigger) {
                    $trigger->setIsMassAction(true); // Do not save chain if it is a mass action
                    $trigger->setIsActive($status)
                        ->save();
                }

                if ($this->getRequest()->isAjax()) {
                    return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                        'success'  => true
                    ]);
                }

                $this->messageManager->addSuccessMessage(
                    __('Total of %1 record(s) were updated', $collection->getSize())
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
