<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;

class Replacetext extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;
    protected $objectManager;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        ResourceConnection $resource
    )
    {
        parent::__construct();
        $this->_helper = $helper;
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->_eavConfig = $eavConfig;
        $this->connection = $resource->getConnection();

        $this->_type = 'replacetext';
        $this->_info = [
            'confirm_title' => 'Replace Text',
            'confirm_message' => 'Are you sure you want to replace text?',
            'type' => $this->_type,
            'label' => 'Replace Text',
            'fieldLabel' => 'Replace',
            'placeholder' => 'search->replace'
        ];
    }

    const REPLACE_MODIFICATOR = '->';

    const REPLACE_FIELD = 'value';

    public function execute($ids, $storeId, $val)
    {
        $searchReplace = $this->_generateReplaces($val);
        $this->_searchAndReplace($searchReplace, $ids, $storeId);

        $success = __('Total of %1 products(s) have been successfully updated.', count($ids));
        return $success;
    }

    /**
     * @param string $inputText
     * @return array
     */
    protected function _generateReplaces($inputText)
    {
        $modificatorPosition = stripos($inputText, self::REPLACE_MODIFICATOR);
        if (false === $modificatorPosition) {
            throw new \Amasty\Paction\Model\CustomException(__('Replace field must contain: search->replace'));
        }
        $result['search'] = trim(
            substr($inputText, 0, $modificatorPosition)
        );
        $result['replace'] = trim(
            substr(
                $inputText, (strlen($result['search']) + strlen(self::REPLACE_MODIFICATOR)),
                strlen($inputText)
            )
        );

        return $result;
    }

    /**
     * @param array $searchReplace
     * @param array $ids
     * @param int $storeId
     *
     * @return string
     */
    protected function _searchAndReplace($searchReplace, $ids, $storeId)
    {
        $attrGroups = $this->_getAttrGroups();

        $db = $this->connection;
        $table = $this->resource->getTableName('catalog_product_entity');
        $entityIdName = $this->_helper->getEntityNameDependOnEdition();

        foreach ($attrGroups as $backendType => $attrIds) {
            if ($backendType == 'static') {
                $set = '';
                foreach ($attrIds as $attrId => $attrName) {
                    $set .= sprintf(
                        '`%s` = REPLACE(`%s`, %s, %s)',
                        $attrName,
                        $attrName,
                        $db->quote($searchReplace['search']),
                        $db->quote($searchReplace['replace'])
                    );
                }
                $sql = sprintf('UPDATE %s
                  SET %s
                  WHERE %s IN(%s)',
                    $table,
                    $set,
                    $entityIdName,
                    implode(',', $ids)
                );
            } else {
                $table = $this->resource->getTableName('catalog_product_entity_' . $backendType);
                $sql = sprintf('UPDATE %s
                  SET `%s` = REPLACE(`%s`, %s, %s)
                  WHERE attribute_id IN (%s)
                    AND %s IN(%s)
                    AND store_id=%d',
                    $table,
                    self::REPLACE_FIELD,
                    self::REPLACE_FIELD,
                    $db->quote($searchReplace['search']),
                    $db->quote($searchReplace['replace']),
                    implode(',', array_keys($attrIds)),
                    $entityIdName,
                    implode(',', $ids),
                    $storeId
                );
            }
            $db->query($sql);
        }

        return true;

    }

    /**
     * @return array
     */
    protected function _getAttrGroups()
    {
        $productAttributes = $this->_helper->getModuleConfig('general/replace_in_attr');
        $productAttributes = explode(',', $productAttributes);

        $attrGroups = [];
        foreach ($productAttributes as $item) {
            $attribute = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $item);
            $attrGroups[$attribute->getBackendType()][$attribute->getId()] = $attribute->getName();
        }
        return $attrGroups;
    }

}

