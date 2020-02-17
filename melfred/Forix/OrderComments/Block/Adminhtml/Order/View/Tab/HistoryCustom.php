<?php

namespace Forix\OrderComments\Block\Adminhtml\Order\View\Tab;

class HistoryCustom extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\History
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Forix_OrderComments::order/view/tab/history.phtml';

    /**
     * @return array
     */
    public function getFullHistory()
    {
        $order = $this->getOrder();

        $history = [];
        foreach ($order->getAllStatusHistory() as $orderComment) {
            $history[] = $this->_prepareHistoryItemList(
                $orderComment->getStatusLabel(),
                $orderComment->getIsCustomerNotified(),
                $this->getOrderAdminDate($orderComment->getCreatedAt()),
                $orderComment->getComment(),
                $orderComment->getIsOrderNote(),
                $orderComment->getIsPoNumber()
            );
        }

        foreach ($order->getCreditmemosCollection() as $_memo) {
            $history[] = $this->_prepareHistoryItemList(
                __('Credit memo #%1 created', $_memo->getIncrementId()),
                $_memo->getEmailSent(),
                $this->getOrderAdminDate($_memo->getCreatedAt())
            );

            foreach ($_memo->getCommentsCollection() as $_comment) {
                $history[] = $this->_prepareHistoryItemList(
                    __('Credit memo #%1 comment added', $_memo->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $this->getOrderAdminDate($_comment->getCreatedAt()),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getShipmentsCollection() as $_shipment) {
            $history[] = $this->_prepareHistoryItem(
                __('Shipment #%1 created', $_shipment->getIncrementId()),
                $_shipment->getEmailSent(),
                $this->getOrderAdminDate($_shipment->getCreatedAt())
            );

            foreach ($_shipment->getCommentsCollection() as $_comment) {
                $history[] = $this->_prepareHistoryItem(
                    __('Shipment #%1 comment added', $_shipment->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $this->getOrderAdminDate($_comment->getCreatedAt()),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getInvoiceCollection() as $_invoice) {
            $history[] = $this->_prepareHistoryItem(
                __('Invoice #%1 created', $_invoice->getIncrementId()),
                $_invoice->getEmailSent(),
                $this->getOrderAdminDate($_invoice->getCreatedAt())
            );

            foreach ($_invoice->getCommentsCollection() as $_comment) {
                $history[] = $this->_prepareHistoryItem(
                    __('Invoice #%1 comment added', $_invoice->getIncrementId()),
                    $_comment->getIsCustomerNotified(),
                    $this->getOrderAdminDate($_comment->getCreatedAt()),
                    $_comment->getComment()
                );
            }
        }

        foreach ($order->getTracksCollection() as $_track) {
            $history[] = $this->_prepareHistoryItem(
                __('Tracking number %1 for %2 assigned', $_track->getNumber(), $_track->getTitle()),
                false,
                $this->getOrderAdminDate($_track->getCreatedAt())
            );
        }

        usort($history, [__CLASS__, 'sortHistoryByTimestamp']);
        return $history;
    }

    /**
     * @param array $item
     * @return bool
     */
    public function isOrderNote(array $item){
        if(isset($item['order_note']) && $item['order_note'] == '1') {
            return true;
        }
        return false;
    }
    /**
     * @param array $item
     * @return bool
     */
    public function isPoNumber(array $item){
        if(isset($item['po_number']) && $item['po_number'] == '1') {
            return true;
        }
        return false;
    }

    /**
     * Map history items as array
     *
     * @param string $label
     * @param bool $notified
     * @param \DateTimeInterface $created
     * @param string $comment
     * @return array
     */
    protected function _prepareHistoryItemList($label, $notified, $created, $comment = '', $orderNote = '' , $ponumber = '')
    {
        return ['title' => $label, 'notified' => $notified,  'comment' => $comment, 'created_at' => $created, 'order_note' => $orderNote, 'po_number' => $ponumber];
    }
}