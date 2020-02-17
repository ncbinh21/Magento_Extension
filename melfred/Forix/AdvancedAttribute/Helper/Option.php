<?php
namespace Forix\AdvancedAttribute\Helper;

class Option extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;
    protected $_backendUrl;
    protected $_eavAttributeRepository;
    protected $_request;
    protected $_bannerOption;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Forix\AdvancedAttribute\Model\OptionFactory $bannerOption
    )
    {

        $this->_storeManager = $storeManager;
        $this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->_eavAttributeRepository = $eavAttributeRepository;
        $this->_request = $request;
        $this->_backendUrl = $backendUrl;
        $this->_bannerOption = $bannerOption;
    }

    /**
     * Retrieve attribute option values for given store id
     *
     * @param int $storeId
     * @return array
     */
    public function getStoreOptionValues($attributeId)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $values = $this->getData('store_option_values_' . $storeId);
        if ($values === null) {
            $values = [];
            $valuesCollection = $this->_attrOptionCollectionFactory->create()->setAttributeFilter(
                $attributeId
            )->setStoreFilter(
                $storeId,
                false
            )->load();
            foreach ($valuesCollection as $item) {
                $values[$item->getId()] = $item->getValue();
            }
            $this->setData('store_option_values_' . $storeId, $values);
        }
        return $values;
    }

    /**
     * Retrieve attribute option values
     *
     * @param string attribute code
     * @return array
     */
    public function getAllOptionAttribute($attributeCode)
    {
        // Access to the attribute interface
        $attribute = $this->_eavAttributeRepository->get(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode);

        $result = array();
        // Get an array of options
        $options = $attribute->getOptions();
        foreach ($options as $option) {
            if ($option->getValue()) {
                $result[$option->getValue()] = $option->getLabel();
            }
        }
        return $result;
    }

    /**
     * Retrieve attribute one option values
     *
     * @param string attribute code
     * @return array
     */
    public function getOptionLabel($optionId)
    {
        $attributeCode = $this->_request->getParam('attrcode');
        // Access to the attribute interface
        $attribute = $this->_eavAttributeRepository->get(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode);

        // Get an array of options
        $options = $attribute->getOptions();
        foreach ($options as $option) {
            if ($option->getValue() == $optionId)
                return $option->getLabel();
        }
        return '';
    }

    /**
     * Get exist banner option for attribute
     *
     * @param string attribute code
     * @return array
     */
    public function getExistBannerOptionsByAttributeId($attributeId) {
        $result = array();
        if (!empty($attributeId)) {
            $collection = $this->_bannerOption->create()
                ->getCollection()
                ->addFieldToFilter('attribute_id', array('eq' => $attributeId));

            if ($collection->getSize()) {
                foreach ($collection as $option) {
                    $result[] = $option->getData('option_id');
                }
            }
        }

        return $result;
    }

    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('attribute_banners/attributes/products', ['_current' => true]);
    }
}