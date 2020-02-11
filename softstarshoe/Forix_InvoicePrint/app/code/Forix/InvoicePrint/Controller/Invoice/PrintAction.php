<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 06
 * Time: 15:36
 */
namespace Forix\InvoicePrint\Controller\Invoice;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order\Pdf\Invoice as PdfInvoice;

use Magento\Framework\Exception\NoSuchEntityException;

class PrintAction  extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    protected $_invoiceRepository;
    protected $_pdfInvoice;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        PdfInvoice $pdfInvoice,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    )
    {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
        $this->_invoiceRepository = $invoiceRepository;
        $this->_pdfInvoice = $pdfInvoice;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            try {
                $invoice = $this->_invoiceRepository->get($invoiceId);
                if ($invoice) {
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

                    /*return $this->_fileFactory->create(
                        'invoice' . $date . '.pdf',
                        $pdf->render(),
                        DirectoryList::VAR_DIR,
                        'application/pdf'
                    );*/
                }
            }catch (\Exception $e){
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $this->resultForwardFactory->create()->forward('noroute');
    }
}