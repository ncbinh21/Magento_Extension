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



namespace Mirasvit\Credit\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Credit extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setBaseCreditTotalRefunded(0)
            ->setCreditTotalRefunded(0)
            ->setBaseCreditReturnMax(0)
            ->setCreditReturnMax(0);

        $order = $creditmemo->getOrder();

        if ($order->getBaseCreditAmount() && $order->getBaseCreditInvoiced()) {
            $left = $order->getBaseCreditInvoiced() - $order->getBaseCreditRefunded();

            if ($left >= $creditmemo->getBaseGrandTotal()) {
                $baseUsed = $creditmemo->getBaseGrandTotal();
                $used = $creditmemo->getGrandTotal();

                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setGrandTotal(0);

                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $baseUsed = $order->getBaseCreditInvoiced() - $order->getBaseCreditRefunded();
                $used = $order->getCreditInvoiced() - $order->getCreditRefunded();

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseUsed);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $used);
            }

            $creditmemo->setBaseCreditAmount($baseUsed);
            $creditmemo->setCreditAmount($used);
        }

        $creditmemo->setBaseCreditReturnMax($creditmemo->getBaseGrandTotal());
        $creditmemo->setCreditReturnMax($creditmemo->getBaseCreditReturnMax());

        return $this;
    }
}
