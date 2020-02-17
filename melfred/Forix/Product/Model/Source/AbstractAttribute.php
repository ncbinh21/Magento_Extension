<?php

namespace Forix\Product\Model\Source;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

class AbstractAttribute extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    protected $_attributeCode;
    /**
     * Store manager interface.
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * EAV Config.
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
    
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->eavConfig = $eavConfig;
    }

    /**
     * Get StoreManager dependency.
     *
     * @return StoreManagerInterface
     * @deprecated
     */
    protected function getStoreManager()
    {
        if ($this->storeManager === null) {
            $this->storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        }

        return $this->storeManager;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function addEmptyOption(array $options)
    {
        if($this->getAttribute()) {
            array_unshift($options, ['label' => $this->getAttribute()->getIsRequired() ? '' : ' ', 'value' => '']);
        }
        return $options;
    }

    /**
     * Get attribute instance
     *
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @codeCoverageIgnore
     */
    public function getAttribute()
    {
        if (is_null($this->_attribute)) {
            $this->_attribute = $this->eavConfig->getAttribute('catalog_product', $this->_attributeCode);
        }
        return $this->_attribute;
    }

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $options = [];
        if($this->getAttribute()) {
            $storeId = $this->getAttribute()->getStoreId();
            if ($storeId === null) {
                $storeId = $this->getStoreManager()->getStore()->getId();
            }
            if (!is_array($this->_options)) {
                $this->_options = [];
            }
            if (!is_array($this->_optionsDefault)) {
                $this->_optionsDefault = [];
            }
            $attributeId = $this->getAttribute()->getId();
            if (!isset($this->_options[$storeId][$attributeId])) {
                $collection = $this->_attrOptionCollectionFactory->create()->setPositionOrder(
                    'asc'
                )->setAttributeFilter(
                    $attributeId
                )->setStoreFilter(
                    $storeId
                )->load();
                foreach ($collection as $_option) {
                    $this->_options[$storeId][$attributeId][] = ['value' => $_option->getId(), 'label' => $_option->getValue()];
                };
                $this->_optionsDefault[$storeId][$attributeId] = $collection->toOptionArray('default_value');
            }
            $options = $defaultValues
                ? $this->_optionsDefault[$storeId][$attributeId]
                : $this->_options[$storeId][$attributeId];
            if ($withEmpty) {
                $options = $this->addEmptyOption($options);
            }
        }

        return $options;
    }
}
