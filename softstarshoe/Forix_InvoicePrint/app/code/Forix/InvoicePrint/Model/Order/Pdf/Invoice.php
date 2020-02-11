<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 07
 * Time: 10:41
 */

namespace Forix\InvoicePrint\Model\Order\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

/**
 * 25 = 33px
 * 1 = 0.757 px (pdf)
 * 1.32px = 1
 * A4 Top = 842
 * Left = 0
 * Bottom = 0
 * page width: 595
 * Class Invoice
 * @package Forix\InvoicePrint\Model\Order\Pdf
 */
class Invoice  extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    protected $_paddingTop = 25;
    protected $_paddingLeft = 25;
    protected $_paddingRight = 25;
    protected $_paddingBottom = 124;
    protected $_headerFontSize = 11;
    protected $_contentFontSize = 12;
    protected $_lineHeight = 12;
    protected $_invoiceHelper;

    protected $_invoice = null;
    protected $_lastTopTable;
    protected $_lastY;
    public function setLastY($lastY){
        $this->_lastY = $lastY;
    }

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Forix\InvoicePrint\Helper\Data $invoiceHelper
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Forix\InvoicePrint\Helper\Data $invoiceHelper,
        array $data = []
    ) {
        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
        $this->_invoiceHelper = $invoiceHelper;
    }

    /**
     * @param $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     */
    public function setFontBold($object, $size = 7){
        return $this->_setFontBold($object, $size);
    }
    
    /**
     * Set font as bold
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/CatamaranFont/Catamaran-Bold.ttf')
        );
        $object->setFont($font, $size);
        $object->setAlpha(1);
        return $font;
    }

    /**
     * @param $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     */
    public function setFontRegular($object, $size = 7){
        return $this->_setFontRegular($object, $size);
    }
    
    /**
     * Set font as italic
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = \Zend_Pdf_Font::fontWithPath(
            $this->_rootDirectory->getAbsolutePath('lib/internal/CatamaranFont/Catamaran-Regular.ttf')
        );
        $object->setFont($font, $size);
        $object->setAlpha(0.8);
        return $font;
    }


    /**
     * @param $order \Magento\Sales\Model\Order
     * @return array
     */
    protected function getGiftCards($order){
        $giftCards = [];
        if($order->getExtensionAttributes()) {
            $giftCards = $order->getExtensionAttributes()->getAwGiftcardCodes();
        }
        return $giftCards;
    }

    /**
     * Get Text With by current font config
     * @param $text
     * @param \Zend_Pdf_Resource_Font $font
     * @param $font_size
     * @return float|int
     */
    private function getTextWidth($text, $font, $font_size) {
        $drawing_text = iconv('', 'UTF-8', $text);
        $characters    = array();
        for ($i = 0, $count = strlen($drawing_text) - 1; $i < $count ; $i++) {
            $characters[] = ord ($drawing_text[$i]);
        }
        $glyphs        = $font->glyphNumbersForCharacters($characters);
        $widths        = $font->widthsForGlyphs($glyphs);
        $text_width   = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;
        return $text_width;
    }


    /**
     * Draw Table Header
     * @param &$page \Zend_Pdf_Page
     * @param int $top
     * @param int $from
     * @param int $to
     * @param int $height
     */
    protected function _drawTableHeader($page, $top, $from = 25, $to = 570, $height = 20){
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.7));
        $page->setLineWidth(0.5);
        $page->drawRectangle($from, $top, $to, $top - $height);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        $this->_lastTopTable = $top - $height;
    }

    /**
     * @param &$page \Zend_Pdf_Page
     * @param $top
     * @param int $from
     * @param int $to
     * @param int $height
     */
    protected function _drawTableBorder($page, $top, $from = 25, $to = 570, $height = 20){

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.7));
        $page->setLineWidth(0.5);
        $page->drawRectangle($from, $top, $to, $top - $height,  \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0)); //Reset Filter Color
        $this->_lastTopTable = false;
    }

    /**
     * Draw Image.
     *  'invoice#.png'|825|412|58|15
     * @param &$page \Zend_Pdf_Page
     */
    protected function _addInvoiceImage($page, $filename, $top, $left, $widthLimit, $heightLimit){
        $imagePath = '/sales/invoice/print/' . $filename;
        if ($this->_mediaDirectory->isFile($imagePath)) {
            $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
            //assuming the image is not a "skyscraper"
            $width = $image->getPixelWidth();
            $height = $image->getPixelHeight();

            //preserving aspect ratio (proportions)
            $ratio = $width / $height;
            if ($ratio > 1 && $width > $widthLimit) {
                $width = $widthLimit;
                $height = $width / $ratio;
            } elseif ($ratio < 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $height * $ratio;
            } elseif ($ratio == 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $widthLimit;
            }

            $y1 = $top - $height;
            $y2 = $top;
            $x2 = $left + $width;

            //coordinates after transformation are rounded by Zend
            $page->drawImage($image, $left, $y1, $x2, $y2);

            //$this->y = $y1 - 10;
        }
    }

    /**
     * {@inheritdoc}
     */
    /*public function getAlignRight($string, $x, $columnWidth, \Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $textWidth = $this->getTextWidth($string, $font, $fontSize);
        return $columnWidth - $x - $textWidth;
    }*/

    /**
     * Draw Alignt Right Text
     * @param $text string
     * @param $page \Zend_Pdf_Page
     * @param $fontSize integer
     * @param $right integer
     * @param $top integer
     * @return \Zend_Pdf_Page
     */
    protected function alightRight($text, $page, $right, $top){
        $font = $page->getFont();
        $fontSize = $page->getFontSize();
        //$x1 = $this->getAlignRight($text, $right, $page->getWidth(), $font, $fontSize);
        $right = $page->getWidth() - $right;
        $x1 = $this->getAlignRight($text, $right, 0, $font, $fontSize, 0);
        $page->drawText($text, $x1, $top, 'UTF-8');
        return $page;
    }

    /**
     * @param $page  \Zend_Pdf_Page
     * @param $invoice \Magento\Sales\Model\Order\Invoice
     */
    public function addInvoiceInfo($page, $invoice){
        if ($order = $invoice->getOrder()) {
            $top = 808;
            $fontSize = 24;
            $this->_setFontBold($page, $fontSize);
            $incrementId = $order->getIncrementId();
            $right = $page->getWidth() - 25;
            $incrementWidth = $this->getAlignRight($incrementId, $right, 0, $page->getFont(), $fontSize, 0);
            $this->_addInvoiceImage($page,'invoice#.png',825, $incrementWidth - 58,58,15);

            /** Draw Order Increment Id */
            $this->_setFontBold($page, $fontSize);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->alightRight($order->getIncrementId(), $page, 25, $top);
            /** Draw Order Billing Last Name */
            $fontSize = 16;
            $top -= $fontSize + 2;
            $this->_setFontRegular($page, $fontSize);
            $lastNameCustomer =  '';
            if($order->getShippingAddress()) {
                $lastNameCustomer = $order->getShippingAddress()->getLastname();
            }
            $this->alightRight(strtoupper($lastNameCustomer), $page, 25, $top);
            $top -= $fontSize + 2;
            
            /** Draw Order Date */
            $this->_setFontRegular($page, $fontSize);
            $this->alightRight(strtoupper(
                $this->_localeDate->formatDate(
                    $this->_localeDate->scopeDate(
                        $order->getStore(),
                        $order->getCreatedAt(),
                        true
                    ),
                    \IntlDateFormatter::MEDIUM,
                    false
                )),
                $page,
                25,
                $top
            );
            $top -= $fontSize + 4;
            $this->y = $top;
        }
    }

    /**
     * Check Draw new is prepend to new page.
     * @param array $draw
     * @return bool
     */
    public function isNewPage(array $draw){
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                return false;
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = [$column['text']];
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < $this->_paddingBottom) {
                return true;
            }
        }
        return false;
    }

    /**
     * Insert logo to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param null $store
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $filename = 'logo.png';
        if ($filename) {
            $imagePath = '/sales/invoice/print/' . $filename;
            if ($this->_mediaDirectory->isFile($imagePath)) {
                $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                $top = 817;
                //top border of the page
                $widthLimit = 172;
                //half of the page width
                $heightLimit = 39;
                //assuming the image is not a "skyscraper"
                $width = $image->getPixelWidth();
                $height = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = $this->_paddingLeft;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 20;
            }
        }
    }

    /**
     * @param $item \Magento\Sales\Model\Order\Invoice\Item
     * @return bool
     */
    public function isBreakNewPage($item){
        $rules = $this->_invoiceHelper->getRuleCollection();
        foreach ($rules as $rule){
            if($rule->validate($item)){
                return true;
            }
        }
        return false;
    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);

        $arrPageInvoice = [];
        $temp = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $this->_invoice = $invoice;
            $order = $invoice->getOrder();
            $page = $this->newPage(['draw_logo' => true]);

            $this->_setFontBold($page, 10);
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());


            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            //$this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());



            /* Add body */
            $firstItem = false;
            /**
             * @var $item \Magento\Sales\Model\Order\Invoice\Item
             */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                if($firstItem) {
                    if(false !== $this->_lastTopTable) {
                        $this->_drawTableBorder($page, $this->_lastTopTable, 25, 570, $this->_lastTopTable - ($this->_lastY ?: $this->y) - $page->getFontSize() - 4);
                    }
                    if($this->isBreakNewPage($item)){
                        $page = $this->newPage();
                    }
                }
                /* Add table */
                $page = $this->_drawHeader($page);

                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
                $firstItem = true;
                $this->y -= 10;
            }
            if($firstItem) {
                if(false !== $this->_lastTopTable) {
                    $this->_drawTableBorder($page, $this->_lastTopTable, 25, 570, $this->_lastTopTable - ($this->_lastY ?: $this->y) - $page->getFontSize() - 4);
                }
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
//            $this->drawFooter($invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }
            $page = end($pdf->pages);
            $this->drawShippingAgreement($page, $invoice);
            $countPage = count($this->_getPdf()->pages);
            $perPage =  $countPage - $temp;
            $arrPageInvoice[] = $perPage;
            $temp = $countPage;
        }
//        $arrPageInvoice
        $this->drawFooter($arrPageInvoice);
        $this->_afterGetPdf();
        return $pdf;
    }


    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return \Zend_Pdf_Page
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, $this->_contentFontSize);
        //columns headers
        $lines[0][] = ['text' => __('Product Name'),'font' => 'bold', 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'font' => 'bold','feed' => 200];

        $lines[0][] = ['text' => __('Original Price'), 'font' => 'bold','feed' => 300];

        $lines[0][] = ['text' => __('Sale Price'),'font' => 'bold', 'feed' => 390];

        $lines[0][] = ['text' => __('Qty'),'font' => 'bold', 'feed' => 470];

        $lines[0][] = ['text' => __('Subtotal'),'font' => 'bold', 'feed' => 510];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->y -= 15;
        $newPage = $this->isNewPage([$lineBlock]);
        $this->y += 15;
        if($newPage){
            $page = $this->newPage();
        }
        $this->_drawTableHeader($page, $this->y, 25, 570);
        $this->y -= 15;
        $page = $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
        return $page;
    }


    /**
     * Draw new invoice header
     * @param \Zend_Pdf_Page &$page
     * @param $invoice \Magento\Sales\Model\Order\Invoice
     * @param $drawLogo boolean
     * @return void
     */
    protected function drawNewHeaderPage($page, $invoice, $drawLogo){
        if($drawLogo) {
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
        }

        $this->addInvoiceInfo($page, $invoice);
    }

    /**
     * Draw pager at footer
     *
     * @param int $top
     * @param int $left
     * @param int $fontSize
     */
    protected function drawPager($sumPage, $startWith, $_page, $top = 30, $left = 477, $fontSize = 17){
//        $sumPage = count($this->_pdf->pages);
//        $startWith = 1;
//        foreach ($this->_pdf->pages as $_page){
        /**
         * @var $_page \Zend_Pdf_Page
         */
        $this->_setFontRegular($_page, $fontSize);

        $text = strip_tags(__("Page %1 of   %2",$startWith, $sumPage));
        $text2 = strip_tags(__("%2",$sumPage));

        $feed2 = $this->getAlignRight($text2, $_page->getWidth() - 35, 0, $_page->getFont(), $fontSize);

//            $_page->drawText($text, $feed, $top, 'UTF-8');
        if($sumPage > 1){
            $this->_drawTableHeader($_page, $top + 18, $feed2 + 12, 570, 25);
        }else{
            $text = strip_tags(__("Page %1 of %2",$startWith, $sumPage));
        }
        $this->alightRight($text, $_page, 35, $top);
//            $startWith ++;
//        }
    }


    /**
     * Draw Footer Of Invoice Page
     * @param &$page \Zend_Pdf_Page
     * @param $invoice \Magento\Sales\Model\Order\Invoice
     */
    protected function drawFooter(array $arrPageInvoice){
        $arraySave = $arrPageInvoice;
        $i = 0;
        $count = count($this->_getPdf()->pages);
        $startWith = 1;
        $noteFontSize = 10;
        foreach ($this->_getPdf()->pages as $page) {
            $this->_setFontRegular($page, $noteFontSize);
            if($arrPageInvoice[$i] == 0) {
                $i++;
                $startWith = 1;
            }
            if(isset($arrPageInvoice[$i])){
                $topTree = 114;
                $this->_addInvoiceImage($page, 'tree.png', $topTree, 295, 40, 51);
                // Break line with 56 characters
                $footerText = $this->_invoiceHelper->getInvoiceFooterMessage();
                $text = [];
                foreach ($this->string->split($footerText, 56, true, true) as $_value) {
                    $text[] = $_value;
                }
                $line = [337,341,342,336];
                $k = 0;
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)),$line[ $k % 4], $topTree - 7, 'UTF-8');
                    $topTree -= 15;
                    $k++;
                }
                $this->drawPager($arraySave[$i], $startWith, $page, 30, 477, 20);
                $startWith ++;
                $arrPageInvoice[$i]--;
            }
        }
    }

    /**
     * /**
     * Draw Shipping Agreement.
     * @param $page \Zend_Pdf_Page
     * @param $invoice  \Magento\Sales\Model\Order\Invoice
     */
    public function drawShippingAgreement($page, $invoice){
        $agreementText = $this->_invoiceHelper->getInvoiceShippingAgreementMessage();
        $countries = $this->_invoiceHelper->getInvoiceShippingAgreementCountry();

        $order = $invoice->getOrder();

        $noteFontSize = 10;
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $order->getShippingAddress();
            if($countryId = $shippingAddress->getCountryId()){
                if(in_array(strtolower($this->_invoiceHelper->getCountryName($countryId)),$countries)){
                    $y = 171;
                    if ($this->y < $y) {
                        $page = $this->newPage();
                    }
                    $this->_setFontRegular($page, $noteFontSize);
                    if ($agreementText !== '') {
                        $text = [];
                        foreach ($this->string->split($agreementText, 70, true, true) as $_value) {
                            $text[] = $_value;
                        }

                        foreach ($text as $part) {
                            $page->drawText(strip_tags(ltrim($part)), 25, $y , 'UTF-8');
                            $y -= 15;
                        }
                    }

                    $this->_drawTableHeader($page,117, 25,147,1);
                    $this->_drawTableHeader($page,117, 171,258,1);
                    $y -= 25;

                    $part = __('Signature');
                    $page->drawText(strip_tags(ltrim($part)), 61, $y , 'UTF-8');
                    $part = __('Date');
                    $page->drawText(strip_tags(ltrim($part)), 198, $y , 'UTF-8');
                }
            }
        }
    }

    /**
     * Calculate address height
     * @param \Zend_Pdf_Page &$page
     * @param  array $address
     * @return int Height
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function calcAddressHeight($page, $address)
    {
        $y = 0;
        foreach ($address as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 55, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += $page->getFontSize();
                }
            }
        }
        return $y;
    }
    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putOrderId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        /*$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);*/
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, $this->_contentFontSize);

        if ($putOrderId) {
            //$page->drawText(__('Order # ') . $order->getRealOrderId(), 35, $top -= 30, 'UTF-8');
            $top +=15;
        }
        $top -= 12;

        $this->_drawTableHeader($page, $top, 25, 275);
        $this->_drawTableHeader($page, $top, 275, 570);
        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);
        // Add Giff Card code or Counpon Code to Payment.

        // End Add Giff Card code or Counpon Code to Payment.

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($this->addressRenderer->format($order->getShippingAddress(), 'pdf'));
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, $this->_headerFontSize);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $this->_setFontRegular($page, $this->_contentFontSize);
        $addressesHeight = $this->calcAddressHeight($page, $billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->calcAddressHeight($page, $shippingAddress));
        }
        $this->_drawTableBorder($page, $this->_lastTopTable, 25, 570,  $addressesHeight + 35);

        $this->y = $top - 35;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 55, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= $this->_lineHeight;
                }
            }
        }

        $addressesEndY = $this->y;
        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 55, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= $this->_lineHeight;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $this->_drawTableHeader($page, $this->y, 25, 275);
            $this->_drawTableHeader($page, $this->y, 275, 570);

            $this->y -= 15;
            $this->_setFontBold($page, $this->_headerFontSize);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method:'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page, $this->_contentFontSize);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 20;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }
        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= $this->_lineHeight;
                }
            }
        }

        $discounts = [];
        if($couponCode =  $order->getDiscountDescription()){
            $discounts[0][] = [
                'text' =>__("Coupon Code: "),
                'feed' => 35
            ];
            $discounts[0][] = [
                'text' => $couponCode,
                'font' => 'bold',
                'feed' => 106
            ];
        }
        $giftCards = $this->getGiftCards($order);
        $i = 1;
        foreach ($giftCards as $giftCard){
            $discounts[$i][] = [
                'text' =>__("Gift Card Code: "),
                'feed' => 35
            ];
            $discounts[$i][] = [
                'text' => $giftCard->getGiftcardCode(),
                'font' => 'bold',
                'feed' => 106
            ];
            $i ++;
        }
        foreach($discounts as $discount){
            foreach ($discount as $item) {
                if(isset($item['font'])){
                    switch ($item['font']){
                        case 'bold':
                            $this->_setFontBold($page, $page->getFontSize());
                            break;
                        default:
                            $this->_setFontRegular($page, $page->getFontSize());
                            break;
                    }
                }
                $split = $this->string->split(strip_tags(trim($item['text'])), 30, false, true);
                foreach ($split as $_value) {
                    $page->drawText($_value, $item['feed'], $yPayments, 'UTF-8');
                    if(count($split) > 1){
                        $yPayments -=  $this->_lineHeight;
                    }
                }
            }
            $yPayments -=  $this->_lineHeight;
        }
        $this->_setFontRegular($page, $page->getFontSize());

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 20;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= $this->_lineHeight;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . __(
                    'Total Shipping Charges:'
                ) . " " . $order->formatPriceTxt(
                    $order->getShippingAmount()
                ) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            
            if (count($tracks)) {

                $this->_drawTableHeader($page, $yShipments, 285, 510);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, $this->_contentFontSize);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, $this->_contentFontSize);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine($this->_paddingLeft, $methodStartY, $this->_paddingLeft, $currentY);
            //left
            $page->drawLine($this->_paddingLeft, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= $this->_lineHeight;
        }
        if($orderNotes = $this->getOrderNotes($order)) {
            if(!is_array($orderNotes)){
                $orderNotes = [$orderNotes];
            }
            foreach ($orderNotes as $orderNote) {
                $this->y += $this->_lineHeight;
                $top = $this->y;
                $notesHeight = 2;
                $text = [];
                if ($orderNote !== '') {
                    foreach ($this->string->split($orderNote, 100, true, true) as $_value) {
                        $text[] = $_value;
                        $notesHeight += $this->_lineHeight;
                    }
                }

                //getVisibleStatusHistory Order Comments
                $this->_drawTableHeader($page, $top);
                $this->_setFontBold($page, $this->_headerFontSize);
                $page->drawText(__('Order Notes:'), 35, $top - 15, 'UTF-8');
                $this->_drawTableBorder($page, $this->_lastTopTable, 25, 570, $this->_contentFontSize + $notesHeight);
                
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                $this->_setFontRegular($page, $this->_contentFontSize);
                $top -= 35;


                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $top, 'UTF-8');
                    $top -= 15;
                }

                $this->y = $top;
                $this->y -= 15;
            }
        }
    }

    /**
     * Get Order Note by Customer
     * @param $order \Magento\Sales\Model\Order
     * @return string
     */
    private function getOrderNotes($order)
    {
        $_history = $order->getStatusHistoryCollection();
        $noteContent = '';
        foreach ($_history as $_historyItem) {
            if ($_historyItem->getData('is_order_note')) {
                $noteContent = $_historyItem->getComment();
                break;
            }
        }
        return $noteContent;
    }

    /**
     * Draw lines
     *
     * Draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {


        $blockTop = $this->y + (2 * $page->getFontSize());
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We don\'t recognize the draw line data. Please define the "lines" array.')
                );
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : $page->getFontSize();

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = [$column['text']];
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < $this->_paddingBottom) {
                $page = $this->newPage($pageSettings);
                $this->y -= 30;
                $blockTop = $this->y + (2 * $page->getFontSize());
            }
            $leftFeedMin = 595;
            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $minFeed = $column['feed'];
                    $fontSize = empty($column['font_size']) ? $this->_contentFontSize : $column['font_size'];
                    if(isset($pageSettings['font_size'])){
                        $fontSize = $pageSettings['font_size'];
                    }
                    if (!empty($column['font_file'])) {
                        $font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = [$column['text']];
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    $maxTextWidth = 0;
                    $textAlign = 'left';
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < $this->_paddingBottom) {
                            $page = $this->newPage($pageSettings);
                            $this->y -= 30;
                            $blockTop = $this->y + (2 * $page->getFontSize());
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        $newMaxTextWidth = $this->widthForStringUsingFontSize($part, $font, $fontSize);
                        $maxTextWidth = ($maxTextWidth < $newMaxTextWidth) ? $newMaxTextWidth : $maxTextWidth;
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                } else {
                                    $feed = $feed - $newMaxTextWidth;
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                            default:
                                break;
                        }
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $minFeed = $feed < $minFeed ? $feed:$minFeed;
                        $top += $lineSpacing;
                    }
                    if(isset($column['draw-border']) && $column['draw-border']){
                        $height = count($column['text']) * $fontSize; //height line distance
                        $padding = 6;
                        $x = $minFeed - $padding; //x
                        $y = $this->y + $height + 2; //y
                        $bottom = $this->y - $height + 2;
                        if($textAlign == 'center'){
                            $maxTextWidth = $maxTextWidth + 6;
                            $x = $minFeed - $padding;
                        }
                        $page->drawRectangle($x, $y, $x + $maxTextWidth + $padding, $bottom, \Zend_Pdf_page::SHAPE_DRAW_STROKE);
                    }
                    $maxHeight = max($top, $maxHeight);
                    $leftFeedMin = min($minFeed , $leftFeedMin); 
                }
                $this->y -= $maxHeight;
                $this->_lastY = $this->y;
            }
            if(isset($itemsProp['draw_border']) && $itemsProp['draw_border']){
                if(595 != $leftFeedMin){
                    $this->_drawTableBorder($page, $blockTop - 10, $leftFeedMin - 10, 570, $blockTop - $this->_lastY - 5);
                }
            }
        }

        return $page;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    protected function insertTotals($page, $source)
    {
        $order = $source->getOrder();
        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15, 'draw_border' => true];
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);

            if ($total->canDisplay()) {
                $total->setFontSize($this->_contentFontSize);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    $font = (false !== strpos($totalData['label'], 'Estimated') || false !== strpos($totalData['label'], 'Grand')) ? 'bold': '';
                    $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => $font
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => $font
                        ],
                    ];
                }
            }
        }
        $this->y -= 20;
        $newPage = $this->isNewPage([$lineBlock]);
        if($newPage){
            $page = $this->newPage();
            $this->y -= 30;
        }
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }
    
    /**
     * {@inheritdoc}
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        /**
         * @var $_page \Zend_Pdf_Page
         */
        $_page = end($this->_getPdf()->pages);
        if($_page) {
            if($this->_lastTopTable) {
                $this->_drawTableBorder($_page, $this->_lastTopTable, 25, 570, $this->_lastTopTable - ($this->_lastY?:$this->y) + $_page->getFontSize() - 4);
            }
        }
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 842;
        $this->y -= 110;
        $drawLogo = !empty($settings['draw_logo']);
        $this->drawNewHeaderPage($page, $this->_invoice, $drawLogo);
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}