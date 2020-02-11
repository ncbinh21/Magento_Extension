<?php
namespace Forix\ProductLabel\Controller\Adminhtml\Rule;

use Forix\ProductLabel\Controller\Adminhtml\AbstractRule;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Forix\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Forix\ProductLabel\Model\RuleFactory;

/**
 * Class MassDelete
 * @package Forix\ProductLabel\Controller\Adminhtml\Rule
 */
class MassDelete extends AbstractRule
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
     * @param RuleFactory $ruleFactory
     * @param Filter $filter
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CollectionFactory $collectionFactory,
        Date $dateFilter,
        RuleFactory $ruleFactory,
        Filter $filter,
        ForwardFactory $resultForwardFactory
    ) {
        $this->_filter = $filter;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $coreRegistry, $dateFilter, $ruleFactory);
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

            /** @var \Forix\ProductLabel\Model\Rule $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deteled.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the rule(s).'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
