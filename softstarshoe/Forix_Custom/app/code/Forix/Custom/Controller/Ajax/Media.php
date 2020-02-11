<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Custom\Controller\Ajax;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Media
 */
class Media extends \Magento\Swatches\Controller\Ajax\Media
{
    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;

    protected $_sessionManager;
    /**
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     */
    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        Context $context,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->_sessionManager = $sessionManager;
        $this->swatchHelper = $swatchHelper;
        parent::__construct($context, $productModelFactory, $swatchHelper);
    }

    /**
     * Get product media for specified configurable product variation
     *
     * @return string
     */
    public function execute()
    {
        $productMedia = [];
        if ($productId = (int)$this->getRequest()->getParam('product_id')) {
            $productMedia = $this->swatchHelper->getProductMediaGallery(
                $this->productModelFactory->create()->load($productId)
            );
        }

        if($this->getRequest()->getParam('isInCategoryPage') == 'true'){
            $this->_sessionManager->setIsInCategoryPage(1);
        }else {
            $this->_sessionManager->setIsInCategoryPage(0);
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($productMedia);
        return $resultJson;
    }
}
