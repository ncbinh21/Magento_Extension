<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: Magento2
 */
namespace Forix\AdvancedPaypal\Rewrite\Magento\Paypal\Model;
class Ipn extends \Magento\Paypal\Model\Ipn {
    protected function _registerPaymentCapture($skipFraudDetection = false)
    {
        if ($this->getRequestData('transaction_entity') == 'auth') {
            die ('ttt');
            return;
        }
        $parentTransactionId = $this->getRequestData('parent_txn_id');
        $this->_importPaymentInformation();
        $payment = $this->_order->getPayment();
        $payment->setTransactionId(
            $this->getRequestData('txn_id')
        );
        $payment->setCurrencyCode(
            $this->getRequestData('mc_currency')
        );
        $payment->setPreparedMessage(
            $this->_createIpnComment('')
        );
        $payment->setParentTransactionId(
            $parentTransactionId
        );
        $payment->setShouldCloseParentTransaction(
            'Completed' === $this->getRequestData('auth_status')
        );
        $payment->setIsTransactionClosed(
            0
        );
        $payment->registerCaptureNotification(
            $this->getRequestData('mc_gross'),
            $skipFraudDetection
        );
        $this->_order->save();

        // notify customer
        $invoice = $payment->getCreatedInvoice();
        if ($invoice && !$this->_order->getEmailSent()) {
            $this->orderSender->send($this->_order);
            $this->_order->addStatusHistoryComment(
                __('You notified customer about invoice #%1.', $invoice->getIncrementId())
            )->setIsCustomerNotified(
                true
            )->save();
        }
    }
}