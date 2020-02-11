<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Yosto\InstagramConnect\Controller\Adminhtml\Template;
use Magento\Ui\Component\MassAction\Filter;
use Yosto\InstagramConnect\Helper\Constant;
use Yosto\InstagramConnect\Model\ResourceModel\Template\CollectionFactory;
/**
 * Class MassDelete
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class MassDelete extends Action
{

    protected $_filter;
    protected $_collectionFactory;
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Action\Context $context
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute when mass deleting
     */
    public function execute()
    {
        /** @var \Yosto\InstagramConnect\Model\ResourceModel\Template\Collection $collection */
        $collection = $this->_filter->getCollection(
            $this->_collectionFactory->create()
        );
        $collectionSize = $collection->getSize();
        try {
            $collection->getConnection()
                ->delete(
                    $collection->getTable(Constant::INSTAGRAM_TEMPLATE_TABLE),
                    "template_id in ("
                    . implode(',', $collection->getAllIds())
                    . ")"
                );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

}