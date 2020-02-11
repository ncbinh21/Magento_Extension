<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 13
 * Time: 19:12
 */
namespace Forix\InvoicePrint\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Controller\Guest\OrderLoader;
use Magento\Sales\Controller\Guest\OrderViewAuthorization;
use Magento\Sales\Model\Order\Pdf\Invoice as PdfInvoice;

class PrintInvoice extends \Magento\Sales\Controller\Guest\PrintInvoice
{
    protected $_pdfInvoice;

    public function __construct(
        Context $context,
        OrderViewAuthorization $orderAuthorization,
        \Magento\Framework\Registry $registry,
        PageFactory $resultPageFactory,
        OrderLoader $orderLoader,
        PdfInvoice $pdfInvoice
    )
    {
        $this->_pdfInvoice = $pdfInvoice;
        parent::__construct($context, $orderAuthorization, $registry, $resultPageFactory, $orderLoader);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        $invoiceId = (int)$this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->_objectManager->create(
                \Magento\Sales\Api\InvoiceRepositoryInterface::class
            )->get($invoiceId);
            $order = $invoice->getOrder();
        } else {
            $order = $this->_coreRegistry->registry('current_order');
        }

        if ($this->orderAuthorization->canView($order)) {
            if (isset($invoice)) {
                $this->_coreRegistry->register('current_invoice', $invoice);

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
            }
            return $this->resultPageFactory->create()->addHandle('print');
        } else {
            return $this->resultRedirectFactory->create()->setPath('sales/guest/form');
        }
    }
}