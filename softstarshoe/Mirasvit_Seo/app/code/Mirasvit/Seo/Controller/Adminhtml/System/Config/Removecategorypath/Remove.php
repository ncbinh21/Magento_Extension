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
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml\System\Config\Removecategorypath;

use Magento\Framework\Controller\Result\JsonFactory;

class Remove extends \Mirasvit\Seo\Controller\Adminhtml\System\Config\Removecategorypath\Validate
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->context = $context;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Seo::seo');
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_validate();
        if ($result === true) {
            $result = $this->_remove();
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData([
            'valid' => 1,
            'message' => $result,
        ]);
    }

    /**
     * Remove Parent Category Path for Category URLs
     *
     * @return \Mirasvit\Seo\Model\Removecategorypath\Remove
     */
    protected function _remove()
    {
        return $this->_objectManager->get('Mirasvit\Seo\Model\Removecategorypath\Remove')
                                    ->removeParentCategoryPath();

    }
}
