<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Distributor\Controller\Adminhtml\Customer;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

/**
 * Class AbstractMassStatus
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Forix_Distributor::customer';

    /**
     * @var string
     */
    protected $redirectUrl = 'forix_distributor/customer/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Return component referer url
     * TODO: Technical dept referer url should be implement as a part of Action configuration in in appropriate way
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl()?: 'forix_distributor/customer/index';
    }

    /**
     * Execute action to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction(AbstractCollection $collection);
}
