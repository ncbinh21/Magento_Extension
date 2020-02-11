<?php
namespace Forix\FanPhoto\Controller\Adminhtml\Photo;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Forix\FanPhoto\Model\ResourceModel\Photo\CollectionFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Forix\FanPhoto\Model\PhotoFactory;

/**
 * Class MassDelete
 * @package \Forix\FanPhoto\Controller\Adminhtml\Photo
 */
class MassDelete extends \Forix\FanPhoto\Controller\Adminhtml\Photo
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    protected $_filter;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     * @param Date $dateFilter
     * @param PhotoFactory $photoFactory
     * @param Filter $filter
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        Date $dateFilter,
        PhotoFactory $photoFactory,
        Filter $filter,
        ForwardFactory $resultForwardFactory
    ) {
        $this->_filter = $filter;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_collectionFactory = $collectionFactory;
	    parent::__construct($context, $coreRegistry);
    }

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collections = $this->_filter->getCollection($this->_collectionFactory->create());
        $totals = 0;
        try {

            /** @var \Forix\FanPhoto\Model\Photo $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deteled.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the photo(s).'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
