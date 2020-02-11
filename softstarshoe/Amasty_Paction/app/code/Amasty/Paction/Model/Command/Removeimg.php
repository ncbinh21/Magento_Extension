<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Filesystem\DirectoryList;

class Removeimg extends Copyimg
{
    const GALLERY_VALUE_TO_ENTITY_TABLE = 'catalog_product_entity_media_gallery_value_to_entity';

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Filesystem $filesystem,
        ResourceConnection $resource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        parent::__construct(
            $helper,
            $objectManager,
            $catalogProductMediaConfig,
            $fileStorageDb,
            $attributeCollectionFactory,
            $mediaConfig,
            $eavConfig,
            $filesystem,
            $resource,
            $productRepository
        );

        $this->_type = 'removeimg';
        $this->_info = [
            'confirm_title'   => 'Remove Images',
            'confirm_message' => 'Are you sure you want to remove images?',
            'type'            => $this->_type,
            'label'           => 'Remove Images',
            'fieldLabel'      => ''
        ];
    }
    
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        if (!is_array($ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please select product(s)'));
        }
        
        // we do not use store id as it is a global action;
        foreach ($ids as $id) {
            /** @var \Magento\Catalog\Model\Product  $product */
            $product = $this->objectManager->create('Magento\Catalog\Model\Product')
                ->load($id);
            $attribute = $product->getResource()->getAttribute('media_gallery');
            $imageAttr = $this->_getAttributesForRemove($product);
            $this->_removeData($attribute->getId(), $id, $product, $imageAttr);
        }        
        
        $success = __('Images and labels have been successfully deleted.');
        
        return $success; 
    }
    
    protected function _removeData($attrId, $productId, $product, $imageAttr)
    {
        $countPic = count($imageAttr);
        $db =$this->connection;
        $table = $this->resource->getTableName('catalog_product_entity_media_gallery');
        $varcharTable = $this->resource->getTableName('catalog_product_entity_varchar');

        for ($i = 0; $i < $countPic; $i++) {
            $attributePic[$i] = $product->getResource()->getAttribute($imageAttr[$i]);
        }

        $entityIdName = $this->_helper->getEntityNameDependOnEdition();
        
        // Delete varchar
        for ($i = 0; $i < $countPic; $i++) {
            $db->delete(
                $varcharTable,
                'attribute_id = ' . $attributePic[$i]->getId() . ' AND ' . $entityIdName . ' = ' . $productId
            );
        }
        
        $valueIds = [];
        // Delete files
        $select = $db->select()
            ->from($table, array('value_id', 'value'))
            ->joinInner(
                ['entity' => $this->resource->getTableName(
                    self::GALLERY_VALUE_TO_ENTITY_TABLE
                )],
                $table . '.value_id = entity.value_id',
                [$entityIdName => $entityIdName]
            )
            ->where('attribute_id = ?', $attrId)
            ->where($entityIdName . ' = ?', $productId);
        foreach ($db->fetchAll($select) as $row) {
            $path = $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getMediaShortUrl($row['value']));
            if (file_exists($path)) {
                unlink($path);
            }
            $valueIds[] = $row['value_id'];
        }
        
        // Delete media
        $db->delete($table, $db->quoteInto('value_id IN(?)', $valueIds));

        // Delete labels
        $tableLabels = $this->resource->getTableName('catalog_product_entity_media_gallery_value');
        $db->delete($tableLabels, $db->quoteInto('value_id IN(?)', $valueIds));
        
        return true;
    }

    protected function _getAttributesForRemove($product)
    {
        // use cached eav config
        $entityTypeId = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection */
        $collection = $this->_attributeCollectionFactory->create();
        $collection->setEntityTypeFilter($entityTypeId)
            ->setAttributeSetFilter($product->getAttributeSetId())
            ->setFrontendInputTypeFilter('media_image');

        $imgCodes = [];
        foreach ($collection as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $imgCodes[] = $attribute->getAttributeCode();
        }

        return $imgCodes;
    }
}
