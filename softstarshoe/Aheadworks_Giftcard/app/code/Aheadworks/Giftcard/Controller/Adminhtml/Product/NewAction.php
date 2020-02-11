<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as TypeGiftcard;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class NewAction
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Product
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_products';

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @param Context $context
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
    }

    /**
     * Create new action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_getSession()->setBackToAwGiftcardGridFlag(true);
        $this->_getSession()->setResetBackToAwGiftcardGridFlag(false);
        return $this->_redirect(
            'catalog/product/new',
            [
                'set' => $this->productFactory->create()->getDefaultAttributeSetId(),
                'type' => TypeGiftcard::TYPE_CODE
            ]
        );
    }
}
