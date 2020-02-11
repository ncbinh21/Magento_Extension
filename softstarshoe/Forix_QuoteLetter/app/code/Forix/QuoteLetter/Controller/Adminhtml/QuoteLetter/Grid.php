<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: Soft Star Shoes
 * Date: 01 Feb 2018
 * Time: 2:06 AM
 */
namespace Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter;

class Grid extends \Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter\Builder  $quoteLetterBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $coreRegistry)
    {
        parent::__construct($context, $quoteLetterBuilder, $resultPageFactory, $coreRegistry);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \Forix\QuoteLetter\Block\Adminhtml\QuoteLetter\Tab\Product::class,
                'category.product.grid'
            )->toHtml()
        );
    }
}
