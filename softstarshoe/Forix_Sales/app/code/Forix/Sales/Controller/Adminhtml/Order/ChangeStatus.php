<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */
namespace Forix\Sales\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Exception\PaymentException;
use Magento\Framework\View\Result\PageFactory;

class ChangeStatus extends \Magento\Sales\Controller\Adminhtml\Order\Create
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Siawing_CombineOrder::combine';

    const SELECTED_PARAM = 'selected';

    const EXCLUDED_PARAM = 'excluded';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesOrderCollectionFactory;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * ChangeStatus constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->filter = $filter;
        $this->orderRepository = $orderRepository;
        parent::__construct(
            $context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory
        );
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // check if the creation of a new customer is allowed
            if (!$this->_authorization->isAllowed('Magento_Customer::manage')
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }


            $orderCollection = $this->salesOrderCollectionFactory->create();
            $collection = $this->filter->getCollection($orderCollection);

            $countOrder = $this->changeStatusOrder($collection->getAllIds());

            $this->_getSession()->clearStorage();
            $this->messageManager->addSuccess(__('You have put %1 order(s) on Processing - Building.', $countOrder));
            $resultRedirect->setPath('sales/*/');
        } catch (PaymentException $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('sales/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // customer can be created before place order flow is completed and should be stored in current session
            $this->_getSession()->setCustomerId($this->_getSession()->getQuote()->getCustomerId());
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('sales/*/');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Order saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('sales/*/');
        }
        return $resultRedirect;
    }

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection|\Magento\Framework\Data\Collection\AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCollection(\Magento\Framework\Data\Collection\AbstractDb $collection)
    {
        $selected = $this->request->getParam(static::SELECTED_PARAM);
        $excluded = $this->request->getParam(static::EXCLUDED_PARAM);

        $isExcludedIdsValid = (is_array($excluded) && !empty($excluded));
        $isSelectedIdsValid = (is_array($selected) && !empty($selected));

        if ('false' !== $excluded) {
            if (!$isExcludedIdsValid && !$isSelectedIdsValid) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please select item(s).'));
            }
        }
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $idsArray = $this->getFilterIds();
        if (!empty($idsArray)) {
            $collection->addFieldToFilter(
                $collection->getIdFieldName(),
                ['in' => $idsArray]
            );
        }
        return $collection;
    }

    /**
     * @param $orderIds
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function changeStatusOrder($orderIds)
    {
        $countOrder = 0;
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            if ($order->getState() != 'processing') {
                throw new \Magento\Framework\Exception\LocalizedException(__('Can\'t build order with status not processing'));
            }
        }
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
            $order->setStatus('processing_invoiced');
            $order->save();
            $countOrder++;
        }
        return $countOrder;
    }
}