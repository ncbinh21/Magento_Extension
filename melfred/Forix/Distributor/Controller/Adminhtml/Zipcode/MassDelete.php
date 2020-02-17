<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE Melfredborzall
 * Date: 20/08/2018
 * Time: 17:12
 */

namespace Forix\Distributor\Controller\Adminhtml\Zipcode;


use Magento\Ui\Component\MassAction\Filter;
use Forix\Distributor\Model\ResourceModel\Zipcode\CollectionFactory;

class MassDelete extends \Forix\Distributor\Controller\Adminhtml\Zipcode
{
    protected $filter;
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->saveLogAction();
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $collection->walk('delete');
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
