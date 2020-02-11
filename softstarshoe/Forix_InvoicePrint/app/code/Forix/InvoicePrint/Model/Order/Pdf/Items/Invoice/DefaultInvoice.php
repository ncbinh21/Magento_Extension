<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 08
 * Time: 17:18
 */
namespace Forix\InvoicePrint\Model\Order\Pdf\Items\Invoice;
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{

    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;
    protected $_lastTopTable;
    protected $_lastBottomTable;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->string = $string;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        $options = $this->getItem()->getOrderItem()->getProductOptions();
        if ($options) {
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
        }
        return $result;
    }

    /**
     * Draw Table Header
     * @param &$page \Zend_Pdf_Page
     * @param int $top
     * @param int $from
     * @param int $to
     * @param int $height
     */
    protected function _drawTableHeader($page, $top, $from = 25, $to = 570, $height = 25){
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
    protected function _drawTableBorder($page, $top, $from = 25, $to = 570, $height = 25){

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.7));
        $page->setLineWidth(0.5);
        $page->drawRectangle($from, $top, $to, $top - $height,  \Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0)); //Reset Filter Color
        $this->_lastBottomTable = $top - $height;
    }
    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        /**
         * @var $item \Magento\Sales\Model\Order\Invoice\Item
         */
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];

        // draw Product name
        $lines[0] = [
            [
                'text' => $this->string->split($item->getName(), 28, true, true),
                'font' => 'bold',
                'feed' => 35
            ]
        ];


        // draw SKU
        $lines[0][] = [
            'text' => $this->string->split($item->getSku(), 13, true, true),
            'feed' => 200,
            'align' => 'left',
        ];

        // draw QTY
        $lines[0][] = ['text' => $item->getQty() * 1,
            'feed' => 475,
            'font' => 1 < $item->getQty()?'bold':'regular',
            'align' => 'center',
            'draw-border' => 1 < $item->getQty()];


        // draw item Prices
        $i = 0;
        $prices = $this->getItemPricesForDisplay();
        foreach ($prices as $priceData) {

            //$originalPrice = $item->getOrderItem()->getOriginalPrice();
            $basePrice = $item->getOrderItem()->getBasePrice();
            /*$sales = $priceData['price'];
            if($originalPrice < $sales){
                $originalPrice = $sales;
            }*/
            if (isset($priceData['label'])) {
                // draw Price label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => 300, 'align' => 'left'];

                $lines[$i][] = ['text' => $order->formatPriceTxt($basePrice), 'feed' => 390, 'align' => 'left'];
                // draw Subtotal label
                $lines[$i][] = ['text' => $priceData['label'], 'feed' => 510, 'align' => 'left'];
                $i++;
            }
            // Draw Original Price
            $lines[$i][] = [
                'text' =>  $order->formatPriceTxt($item->getOrderItem()->getOriginalPrice()),
                'feed' => 300,
                'align' => 'left',
            ];

            // draw Price
            $lines[$i][] = [
                'text' => $priceData['price'],
                'feed' => 390,
                'align' => 'left',
            ];

            // draw Subtotal
            $lines[$i][] = [
                'text' => $priceData['subtotal'],
                'feed' => 510,
                'align' => 'left',
            ];
            $i++;
        }
        $orderItem = $item->getOrderItem();
        $childItem = $orderItem;
        if(count($orderItem->getChildrenItems())){
            $childItem = current($orderItem->getChildrenItems());
        }
        // draw Invoice Comment
        $lines[]= [
            [
                'text' => $this->string->split($childItem->getData('sss_product_comment'), 100),
                'feed' => 35,
                'align' => 'left',
            ]
        ];

        $lineBlock = ['lines' => $lines, 'height' => 25];
        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $pdf->y -= 12;
        //draw options
        $options = $this->getItemOptions();
        if ($options) {
            $drawHead = true;
            $this->_lastBottomTable = $pdf->y + 15;
            foreach ($options as $option) {
                // draw options label
                $optionLine = [];
                $optionLine[0][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 20, true, true),
                    'feed' => 164,
                    'align' => 'right',
                    'is_options' => true
                ];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = html_entity_decode($option['print_value'], ENT_QUOTES);
                    } else {
                        $printValue = $this->filterManager->stripTags(html_entity_decode($option['value'], ENT_QUOTES));
                    }
                    $font = 'regular';
                    $lower = strtolower($printValue);
                    if(false !== strpos(strtolower('WIDE'),$lower)|| false !== strpos(strtolower('NARROW'),$lower)){
                        $font = 'bold';
                    }
                    $optionLine[0][] = [
                        'text' => $this->string->split($printValue, 50, true, true),
                        'feed' => 189,
                        'align' => 'left',
                        'font' => $font
                    ];
                }
                $lineBlock = ['lines' => $optionLine, 'height' => 20];
                $page = $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true, 'font_size' => 11], $drawHead);
                $drawHead = !$drawHead;
            }
        }
        $this->setPage($page);
    }
    
    public function drawLineBlocks($page, array $draw, array $pageSettings = [], $drawHead)
    {
        /**
         * @var $pdf \Magento\Sales\Model\Order\Pdf\AbstractPdf
         * @var $page \Zend_Pdf_Page
         */
        $pdf = $this->getPdf();
        $maxHeight = 0;
        $pdf->setFontRegular($page, $pageSettings['font_size']);
        foreach ($draw as &$itemsProp) {
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : $page->getFontSize();
            if (empty($itemsProp['shift'])) {
                foreach ($lines as $line) {
                    foreach ($line as $column) {
                        $top = 0;
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = [$column['text']];
                        }
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                }
                $itemsProp['shift'] = $maxHeight;
            }
        }
        if($pdf->isNewPage($draw)) {
            $page = $pdf->newPage($pageSettings);
            $this->_lastBottomTable = $pdf->y + $page->getFontSize();
            $this->_lastTopTable = $pdf->y + $page->getFontSize();
        }
        if($drawHead) {
            $y = $this->_lastBottomTable;
            $this->_drawTableHeader($page, $y, 50, 178, $maxHeight, 25);
            $this->_drawTableHeader($page, $y, 178, 570, $maxHeight, 25);
        }else{
            $y = $this->_lastTopTable;
            $this->_drawTableBorder($page, $y, 50, 178, $maxHeight, 25);
            $this->_drawTableBorder($page, $y, 178, 570, $maxHeight, 25);
        }
        $pdf->drawLineBlocks($page, $draw, $pageSettings);
        return $page;
    }
}