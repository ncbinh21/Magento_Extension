<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;

class Copyattr extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * Eav config
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        ResourceConnection $resource
    ) {
        parent::__construct();
        $this->_helper = $helper;
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->_eavConfig = $eavConfig;
        $this->connection = $resource->getConnection();

        $this->_type = 'copyattr';
        $this->_info = [
            'confirm_title' => 'Copy Attributes',
            'confirm_message' => 'Are you sure you want to copy attributes?',
            'type' => $this->_type,
            'label' => 'Copy Attributes',
            'fieldLabel' => 'From',
            'placeholder' => 'product id'
        ];
    }

    /**
     * @param $ids
     * @param $storeId
     * @param $val
     * @return \Magento\Framework\Phrase
     * @throws \Amasty\Paction\Model\CustomException
     */
    public function execute($ids, $storeId, $val)
    {
        $fromId = intVal(trim($val));
        if (!$fromId) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid product ID'));
        }

        if (in_array($fromId, $ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please remove source product from the selected products'));
        }
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
            ->setStoreId($storeId)
            ->load($fromId);
        if (!$product->getId()) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid product ID'));
        }

        // check attributes
        $codes = $this->_helper->getModuleConfig('general/attr', $storeId);
        if (!$codes) {
            throw new \Amasty\Paction\Model\CustomException(__('Please set attribute codes in the module configuration'));
        }

        $config = [];

        $codes = explode(',', $codes);
        foreach ($codes as $code) {
            $code = trim($code);
            /** @var \Magento\Catalog\Model\Product $model */
            $model = $this->objectManager->create('Magento\Catalog\Model\Product');
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $model->getResource()->getAttribute($code);

            if (!$attribute || !$attribute->getId()) {
                throw new \Amasty\Paction\Model\CustomException(
                    __('There is no product attribute with code `%s`, please compare values in the module configuration with stores > attributes > product.', $code)
                );
            }

            if ($attribute->getIsUnique()) {
                throw new \Amasty\Paction\Model\CustomException(
                    __('Attribute `%s` is unique and can not be copied. Please remove the code in the module configuration.', $code)
                );
            }

            $type = $attribute->getBackendType();
            if ('static' == $type) {
                throw new \Amasty\Paction\Model\CustomException(
                    __('Attribute `%s` is static and can not be copied. Please remove the code in the module configuration.', $code)
                );
            }

            if (!isset($config[$type])) {
                $config[$type] = [];
            }

            $config[$type][] = $attribute->getId();
        }

        // we do not use store id as it is global action
        $this->_copyData($fromId, $ids, $config);

        $success = __('Attributes have been successfully copied.');

        return $success;
    }

    protected function _copyData($fromId, $ids, $config)
    {
        $entityIdName = $this->_helper->getEntityNameDependOnEdition();
        $db = $this->connection;

        foreach ($config as $type => $attributes) {
            if (!$attributes) {
                continue;
            }
            $attributes = implode(',', $attributes);

            $table = $this->resource->getTableName('catalog_product_entity_' . $type);
            foreach ($ids as $id) {
                $sql = "INSERT INTO $table (`attribute_id`, `store_id`, `$entityIdName`, `value`) "
                    . " SELECT  t.`attribute_id`, t.`store_id`, $id, t.`value`"
                    . " FROM $table AS t "
                    . " WHERE t.`$entityIdName`=$fromId AND t.`attribute_id` IN($attributes)"
                    . " ON DUPLICATE KEY UPDATE `value` = t.`value`";
                $db->query($sql);
            }
        }

        return true;
    }

}
