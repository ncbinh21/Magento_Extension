<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/09/2018
 * Time: 11:13
 */
namespace Forix\ProductWizard\Block\Adminhtml\Group\Item;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'group/item/assign_products.phtml';

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

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
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
                \Forix\ProductWizard\Block\Adminhtml\Group\Item\Product::class,
                'group.item.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $products = $this->getGroupItem()->getProductIds();
        if (!empty($products)) {
            return $this->jsonEncoder->encode(array_combine($products,$products));
        }
        return '{}';
    }


    /**
     * @return \Forix\ProductWizard\Model\GroupItem|null
     */
    public function getGroupItem()
    {
        return $this->registry->registry('forix_productwizard_group_item');
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
}