<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 16:55
 */

namespace Forix\AdvanceShipping\Model\Cart;

use \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface;


class ShippingMethod extends \Magento\Quote\Model\Cart\ShippingMethod implements ShippingMethodInterface
{

    /**
     * Returns the shipping method note.
     *
     * @return string Shipping method note.
     */
    public function getMethodNote()
    {
        return $this->_get(self::KEY_METHOD_NOTE);
    }

    /**
     * Sets the shipping method note.
     *
     * @param string $methodNote
     * @return $this
     */
    public function setMethodNote($methodNote)
    {
        return $this->setData(self::KEY_METHOD_NOTE, $methodNote);
    }
}