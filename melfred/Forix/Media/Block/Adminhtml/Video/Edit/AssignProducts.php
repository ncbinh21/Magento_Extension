<?php

namespace Forix\Media\Block\Adminhtml\Video\Edit;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'video/assign_products.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;


    protected $_productCollectionFactory;

    /**
     * AssignProducts constructor.
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Forix\Media\Model\ResourceModel\Video\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Forix\Media\Model\ResourceModel\Video\CollectionFactory $productCollectionFactory, //your custom collection
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {

        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Forix\Media\Block\Adminhtml\Video\Edit\Tab\Product',
                'category.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {

        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {

        $vProducts = $this->_productCollectionFactory->create()
            ->addFieldToSelect('product_id');
        $products = array();
        foreach($vProducts as $pdct){
            $products[$pdct->getProductId()]  = '';
        }

        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}';
    }
    /**
     * @return string
     */
    public function getProductsJsonCustom()
    {

//        $vProducts = $this->_productCollectionFactory->create()
//            ->addFieldToSelect('product_id');
        $video = $this->getItem();
        $products = array();
        if($video->getProductId()){
            $products[$video->getProductId()]  = $video->getProductId();
            try {
                if($this->productRepository->getById($video->getProductId())) {
                    if (!empty($products)) {
                        return $this->jsonEncoder->encode($products);
                    }
                }
            } catch (\Exception $exception) {
                return '{}';
            }
        }
        return '{}';
    }

    public function getItem()
    {
        return $this->registry->registry('forix_media_video');
    }
}