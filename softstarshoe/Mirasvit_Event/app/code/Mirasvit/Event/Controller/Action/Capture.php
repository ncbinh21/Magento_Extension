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
 * @package   mirasvit/module-event
 * @version   1.1.10
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\Controller\Action;

use Magento\Backend\App\Action\Context;
use Magento\Checkout\Model\CartFactory;
use Magento\Quote\Model\Quote;

class Capture extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CartFactory
     */
    protected $cartFactory;

    public function __construct(
        CartFactory $cartRepository,
        Context $context
    ) {
        $this->cartFactory = $cartRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $value = $this->getRequest()->getParam('value');

        /** @var Quote $quote */
        $quote = $this->cartFactory->create()->getQuote();
        if ($quote->getBillingAddress() && $quote->getBillingAddress()->getId()) {
            $billing = $quote->getBillingAddress();
            $billing->setData($type, $value);
            $billing->save();
        }
    }
}
