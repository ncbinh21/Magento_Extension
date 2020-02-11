<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 13
 * Time: 19:12
 */
namespace Forix\InvoicePrint\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Model\Order\Pdf\Invoice as PdfInvoice;

class PrintInvoice extends \Magento\Sales\Controller\Order\PrintInvoice
{
    protected $_pdfInvoice;

    public function __construct(
        Context $context,
        OrderViewAuthorizationInterface $orderAuthorization,
        \Magento\Framework\Registry $registry,
        PdfInvoice $pdfInvoice,
        PageFactory $resultPageFactory)
    {
        $this->_pdfInvoice = $pdfInvoice;
        parent::__construct($context, $orderAuthorization, $registry, $resultPageFactory);
    }

    /**
     * Print Invoice Action
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $invoiceId = (int)$this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->_objectManager->create(
                \Magento\Sales\Api\InvoiceRepositoryInterface::class
            )->get($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $orderId = (int)$this->getRequest()->getParam('order_id');
            $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
        }

        if ($this->orderAuthorization->canView($order)) {
            $this->_coreRegistry->register('current_order', $order);
            if (isset($invoice)) {
                $this->_coreRegistry->register('current_invoice', $invoice);
            }
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $pdf = $this->_pdfInvoice->getPdf([$invoice]);
            $date = $this->_objectManager->get(
                \Magento\Framework\Stdlib\DateTime\DateTime::class
            )->date('Y-m-d_H-i-s');
            $fileName = 'invoice' . $date . '.pdf';
            $content = $pdf->render();
            $this->_response->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-type', 'application/pdf', true)
                ->setHeader('Content-Length', strlen($content), true)
                ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"', false)
                ->setHeader('Last-Modified', date('r'), true);
            $this->_response->sendHeaders();
            echo $content;
            return $this->_response;
        } else {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            if ($this->_objectManager->get(\Magento\Customer\Model\Session::class)->isLoggedIn()) {
                $resultRedirect->setPath('*/*/history');
            } else {
                $resultRedirect->setPath('sales/guest/form');
            }
            return $resultRedirect;
        }
    }
}