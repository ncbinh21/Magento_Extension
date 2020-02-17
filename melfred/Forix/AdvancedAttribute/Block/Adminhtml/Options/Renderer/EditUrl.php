<?php
namespace Forix\AdvancedAttribute\Block\Adminhtml\Options\Renderer;

class EditUrl extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_helperOption;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Forix\AdvancedAttribute\Helper\Option $helperOption,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->_helperOption = $helperOption;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $attributeCode = $this->getRequest()->getParam('attrcode');

        return "<a href='" .
            $this->getUrl('*/*/edit', ['id' => $row->getBannerId() ,
                                        'attrid' => $row->getAttributeId(), 'attrcode' => $attributeCode
            ])
                . "' class='edit-url'>Edit</a>";
    }
}