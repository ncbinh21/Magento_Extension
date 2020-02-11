<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Adminhtml\Product;

/**
 * Class Edit
 *
 * @package Aheadworks\Giftcard\Controller\Adminhtml\Product
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Giftcard::giftcard_products';

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_getSession()->setBackToAwGiftcardGridFlag(true);
        $this->_getSession()->setResetBackToAwGiftcardGridFlag(false);

        $awGcParams = [];
        foreach ($this->getRequest()->getParams() as $key => $value) {
            $result = strpos($key, 'awgc');
            if ($result === 0) {
                $awGcParams[$key] = $value;
            }
        }
        return $this->_redirect(
            'catalog/product/edit',
            array_merge(
                [
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                ],
                $awGcParams
            )
        );
    }
}
