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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Controller\Adminhtml\Redirect;

use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Seo\Model\ResourceModel\Rewrite\CollectionFactory;

class MassEnable extends \Mirasvit\Seo\Controller\Adminhtml\Redirect
{
    /**
     * @param \Mirasvit\Seo\Model\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Registry         $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mirasvit\Seo\Model\RedirectFactory $redirectFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($redirectFactory, $registry, $context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam('redirect_id')) {
            $ids = $this->getRequest()->getParam('redirect_id');
        }

        if ($this->getRequest()->getParam(Filter::SELECTED_PARAM)) {
            $ids = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        }

        if (!$ids) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $ids = $collection->getAllIds();
        }

        if (!is_array($ids)) {
            $this->messageManager->addError(__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = $this->redirectFactory->create()
                        ->load($id)
                        ->setIsActive(true);
                    $model->save();
                }
                $this->messageManager->addSuccess(
                    __(
                        'Total of %1 record(s) were successfully enabled',
                        count($ids)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
