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



namespace Mirasvit\Seo\Controller\Adminhtml\Template;

use Magento\Ui\Component\MassAction\Filter;
use Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory;

class MassDisable extends \Mirasvit\Seo\Controller\Adminhtml\Template
{
    /**
     * MassDisable constructor.
     * @param \Mirasvit\Seo\Api\Service\CompatibilityServiceInterface $compatibilityService
     * @param \Mirasvit\Seo\Model\TemplateFactory $templateFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Mirasvit\Seo\Api\Service\MessageInterface $message
     */
    public function __construct(
        \Mirasvit\Seo\Api\Service\CompatibilityServiceInterface $compatibilityService,
        \Mirasvit\Seo\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Mirasvit\Seo\Api\Service\MessageInterface $message
    ) {
        parent::__construct($compatibilityService, $templateFactory, $registry, $context, $message);
        $this->compatibilityService = $compatibilityService;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $ids = [];

        if ($this->getRequest()->getParam('template_id')) {
            $ids = $this->getRequest()->getParam('template_id');
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
                    $model = $this->templateFactory->create()
                        ->load($id)
                        ->setIsActive(false);
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
