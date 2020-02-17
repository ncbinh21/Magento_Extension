<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Adminhtml\Form\Renderer\Fieldset;

use Magento\Framework\Data\Form\Element\Factory;
use Magento\Store\Model\Store;

class MultiStore extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element
{
    /**
     * @var string
     */
    protected $_template = 'form/renderer/fieldset/multistore.phtml';

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * ReferenceResource constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Factory $elementFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->registry = $registry;
    }

    /**
     * @param $storeId
     * @return null|string
     */
    public function getStoreValue($storeId)
    {
        if ($value = $this->getElement()->getValue()) {
            if (preg_match('^([adObis]:|N;)^', $value)) {
                $value = \Zend_Serializer::unserialize($value);
                if (is_array($value)) {
                    $value = isset($value[$storeId])
                        ? $value[$storeId]
                        : (isset($value[Store::DEFAULT_STORE_ID]) ? $value[Store::DEFAULT_STORE_ID] : null);
                }
            }
        }

        return $value;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->_storeManager->getStores();
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return bool
     */
    public function isDefaultStore(\Magento\Store\Api\Data\StoreInterface $store)
    {
        return $store->getStoreId() == Store::DEFAULT_STORE_ID;
    }
}
