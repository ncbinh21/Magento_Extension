<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Giftcard\Model\Email\Previewer;

/**
 * Class Preview
 *
 * @package Aheadworks\Giftcard\Controller\Product
 */
class Preview extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Previewer
     */
    private $previewer;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Previewer $previewer
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Previewer $previewer
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->previewer = $previewer;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $jsonData = [
            'success' => true,
            'content' => ''
        ];
        $storeId = $this->getRequest()->getParam('store');
        $productId = $this->getRequest()->getParam('product');
        $data = $this->getRequest()->getPostValue();
        try {
            $jsonData['content'] = $this->previewer->getPreview($data, $storeId, $productId);
        } catch (LocalizedException $e) {
            $jsonData['success'] = false;
            $jsonData['content'] = $e->getMessage();
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($jsonData);
    }
}
