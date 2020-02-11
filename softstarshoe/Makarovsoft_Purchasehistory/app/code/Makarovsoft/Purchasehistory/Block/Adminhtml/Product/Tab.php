<?php
namespace Makarovsoft\Purchasehistory\Block\Adminhtml\Product;

class Tab extends \Magento\Backend\Block\Widget\Tab
{
    /**
     * @var  \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_orderItemFactory = $orderItemFactory;

        if (!$this->_request->getParam('id')) {
            $this->setCanShow(false);
        }
    }

    public function getTabLabel()
    {
        $cnt = $this->_orderItemFactory->create()->getCollection()
            ->addFieldToFilter('product_id', $this->getRequest()->getParam('id'))
            ->count();

        return __('Product Orders (%1)', $cnt);
    }
}
