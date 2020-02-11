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
 * @package   mirasvit/module-email-designer
 * @version   1.0.16
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Api\Service\Variable;


use Mirasvit\EmailDesigner\Api\Service\VariableProviderInterface;

interface OrderVariableInterface extends VariableProviderInterface
{
    /**
     * Retrieve order model.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder();

    /**
     * Get customer's name. Retrieve strategy:
     * 1. From context, if null and $order is null:
     * 2. From order, if null:
     * 3. From billing address, if null:
     * 4. From shipping address, if null:
     * 5. Empty.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getCustomerName(\Magento\Sales\Model\Order $order = null);
}
