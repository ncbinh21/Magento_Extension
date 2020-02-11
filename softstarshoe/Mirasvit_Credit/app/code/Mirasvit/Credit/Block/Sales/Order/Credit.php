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



namespace Mirasvit\Credit\Block\Sales\Order;

use Magento\Framework\View\Element\Template;
use Magento\Framework\DataObject;

class Credit extends Template
{
    /**
     * @return $this
     */
    public function initTotals()
    {
        if (floatval($this->getSource()->getCreditAmount()) == 0) {
            return $this;
        }

        $total = new DataObject([
            'code'       => $this->getNameInLayout(),
            'block_name' => $this->getNameInLayout(),
            'area'       => $this->getArea(),
        ]);

        $this->getParentBlock()->addTotal($total, $this->getAfterTotal());

        return $this;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
