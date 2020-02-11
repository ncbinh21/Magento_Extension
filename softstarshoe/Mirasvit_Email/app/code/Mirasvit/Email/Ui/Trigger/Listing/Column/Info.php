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



namespace Mirasvit\Email\Ui\Trigger\Listing\Column;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;
use Mirasvit\Email\Helper\Data;
use Mirasvit\Event\Model\Rule;
use Mirasvit\Event\Model\RuleFactory;

class Info extends AbstractColumn
{
    /**
     * @var TriggerRepositoryInterface
     */
    private $triggerRepository;
    /**
     * @var RuleFactory
     */
    private $ruleFactory;
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Data $helper,
        RuleFactory $ruleFactory,
        TriggerRepositoryInterface $triggerRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->helper = $helper;
        $this->ruleFactory = $ruleFactory;
        $this->triggerRepository = $triggerRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareItem(array $item)
    {
        $result = [];
        $trigger = $this->triggerRepository->get($item['trigger_id']);

        $result[] = "<p>{$trigger->getTitle()}</p>";

        if ($trigger->getIsAdmin()) {
            $result[] = "<p><b>Administrator Trigger</b></p>";
            $result[] = "<p>Mail to: <b>{$trigger->getAdminEmail()}</b></p>";
        }

        foreach ($trigger->getChainCollection() as $chain) {
            $result[] = "<p style='margin-left: 25px;'><small>{$chain->toString()}</small></p>";
        }

        $result[] = "<p style='margin-left: 45px;'><small>{$this->getRule($trigger)->toString()}</small></p>";
        return implode($result);
    }

    /**
     * Get trigger rule with loaded conditions.
     *
     * @param TriggerInterface $trigger
     *
     * @return Rule
     */
    private function getRule(TriggerInterface $trigger)
    {
        /** @var Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule->loadPost($trigger->getRule());

        return $rule;
    }
}
