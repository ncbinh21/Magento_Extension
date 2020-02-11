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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Model;

use Magento\Rule\Model\AbstractModel;

/**
 * @method getName()
 * @method getIsActive()
 * @method getStoreIds()
 * @method getGroupIds()
 * @method getIsStopProcessing()
 * @method getSortOrder()
 * @method getEarningType()
 * @method getEarningAmount()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Earning extends AbstractModel
{
    /**
     * @var Earning\Condition\CombineFactory
     */
    protected $earningConditionCombineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $conditionProductCombineFactory;

    /**
     * @var Earning\Action\CollectionFactory
     */
    protected $earningActionCollectionFactory;

    public function __construct(
        Earning\Condition\CombineFactory $earningConditionCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $conditionProductCombineFactory,
        Earning\Action\CollectionFactory $earningActionCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {

        $this->earningConditionCombineFactory = $earningConditionCombineFactory;
        $this->conditionProductCombineFactory = $conditionProductCombineFactory;
        $this->earningActionCollectionFactory = $earningActionCollectionFactory;

        parent::__construct($context, $registry, $formFactory, $localeDate);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Credit\Model\ResourceModel\Earning');
    }

    /**
     * @return Earning\Condition\Combine
     */
    public function getConditionsInstance()
    {
        $combine = $this->earningConditionCombineFactory->create();

        return $combine;
    }

    /**
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->conditionProductCombineFactory->create();
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    public function getEarningItemAmount($item)
    {
        if ($this->getEarningType() == 'fixed') {
            return $this->getEarningAmount();
        } else {
            return $item->getBaseRowTotalInclTax() * ($this->getEarningAmount() / 100);
        }
    }
}
