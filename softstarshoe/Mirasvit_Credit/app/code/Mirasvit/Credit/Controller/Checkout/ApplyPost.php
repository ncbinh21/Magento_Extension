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



namespace Mirasvit\Credit\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;

class ApplyPost extends \Mirasvit\Credit\Controller\Checkout
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_processPost();

        $url = $this->_url->getUrl('checkout/cart', ['_secure' => true]);
        if ($this->getRequest()->getParam('is_paypal')) {
            $url = $this->_url->getUrl('paypal/express/review', ['_secure' => true]);
        }

        return parent::_goBack($url);
    }
}
