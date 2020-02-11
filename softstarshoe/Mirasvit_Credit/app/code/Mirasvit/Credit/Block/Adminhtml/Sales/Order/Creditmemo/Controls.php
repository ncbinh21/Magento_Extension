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



namespace Mirasvit\Credit\Block\Adminhtml\Sales\Order\Creditmemo;

class Controls extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Credit\Helper\Data
     */
    protected $creditData;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Mirasvit\Credit\Helper\Data $creditData,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->context = $context;
        $this->creditData = $creditData;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function canRefundToCredit()
    {
        if ($this->registry->registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getReturnValue()
    {
        $max = round($this->registry->registry('current_creditmemo')->getCreditReturnMax(), 2);

        if ($max) {
            return $max;
        }

        return 0;
    }

    /**
     * @return \Mirasvit\Credit\Helper\Data
     */
    public function getCreditData()
    {
        return $this->creditData;
    }
}
