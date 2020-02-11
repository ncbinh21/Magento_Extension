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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\LayoutInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;
use Mirasvit\Event\Ui\Event\Block\Rule as RuleBlock;
use Mirasvit\Email\Controller\Adminhtml\Trigger;

class Rule extends Trigger
{
    /**
     * @var RuleBlock
     */
    private $ruleBlock;

    public function __construct(
        RuleBlock $ruleBlock,
        LayoutInterface $layout,
        \Magento\Framework\Stdlib\DateTime\Filter\DateTime $dateTimeFilter,
        TriggerRepositoryInterface $triggerRepository,
        Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($dateTimeFilter, $triggerRepository, $registry, $context);

        $ruleBlock->setLayout($layout);
        $this->ruleBlock = $ruleBlock;
    }


    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $model    = $this->initModel();
        $event    = $this->getRequest()->getParam('event');
        $formName = $this->getRequest()->getParam('formName', 'email_trigger_form');

        $this->registry->register('event_formName', $formName);
        $this->registry->register('event_eventIdentifier', $event);

        if ($model->getId() && $event === $model->getEvent()) {
            $this->registry->register('event_ruleConditions', $model->getRule());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
            'html'    => $this->ruleBlock->toHtml(),
            'success' => true
        ]);
    }
}
