<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;

class Copyimg extends \Amasty\Paction\Model\Command
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

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Catalog product media config
     *
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $_catalogProductMediaConfig;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $fileStorageDb = null;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Attribute collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

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
        parent::__construct();
        $this->_helper = $helper;
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->_catalogProductMediaConfig = $catalogProductMediaConfig;
        $this->fileStorageDb = $fileStorageDb;
        $this->_eavConfig = $eavConfig;
        $this->connection = $resource->getConnection();
        $this->mediaConfig = $mediaConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
        $this->productRepository = $productRepository;

        $this->_type = 'copyimg';
        $this->_info = [
            'confirm_title'   => 'Copy Images',
            'confirm_message' => 'Are you sure you want to copy images?',
            'type'            => $this->_type,
            'label'           => 'Copy Images',
            'fieldLabel'      => 'From',
            'placeholder'     => 'product id'
        ];
    }

    protected $_dImgCodes = [];

    
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
        $success = '';
        
        $fromId = intVal(trim($val));
        if (!$fromId) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid product ID'));
        }
        
        if (in_array($fromId, $ids)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please remove source product from the selected products'));
        }

        /** @var \Magento\Catalog\Model\Product  $product */
        $product = $this->productRepository->getById($fromId);

        if (!$product->getId()) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid product ID'));
        }
        
        // we do not use store id as it is a global action;
        $attribute = $product->getResource()->getAttribute('media_gallery');
        foreach ($ids as $id) {
            $imageAttr = $this->_getAttributes($product, $id);
            $this->_copyData($attribute->getId(), $fromId, $id, $product, $imageAttr);
        }        
        
        $success = __('Images and labels have been successfully copied.');
        
        return $success; 
    }

    protected function _copyData($attrId, $originalProductId, $newProductId, $product, $imageAttr)
    {
        $countPic = count($imageAttr);
        $db = $this->connection;
        $table = $this->resource->getTableName('catalog_product_entity_media_gallery');
        $tableValueToEntity = $this->resource->getTableName('catalog_product_entity_media_gallery_value_to_entity');
        $varcharTable = $this->resource->getTableName('catalog_product_entity_varchar');
        $entityIdName = $this->_helper->getEntityNameDependOnEdition();

        // definition picture path
        for ($i = 0; $i < $countPic; $i++) {
            $newPic[$i] = 'no_selection';
            $attributePic[$i] = $product->getResource()->getAttribute($imageAttr[$i]);
        }
                     
        $select = $db->select()
            ->from($table, array('value_id', 'value'))->joinInner(
                ['entity' => $this->resource->getTableName(\Magento\Catalog\Model\ResourceModel\Product\Gallery::GALLERY_VALUE_TO_ENTITY_TABLE)],
                $table . '.value_id = entity.value_id',
                [$entityIdName => $entityIdName]
            )
            ->where('attribute_id = ?', $attrId)
            ->where($entityIdName . ' = ?', $originalProductId);

        $valueIdMap = [];

        // select old position of basic, small and thumb
        for ($i = 0; $i < $countPic; $i++) {
            $selectPic[$i] = $db->select()
                ->from($varcharTable, array('value', 'store_id'))
                ->where('attribute_id = ?', $attributePic[$i]->getId())
                ->where($entityIdName .' = ?', $originalProductId);
            $picOrig[$i] = $db->fetchRow($selectPic[$i]);

            $storeId[$i] =  $picOrig[$i]['store_id'];
            
            $selectPicId[$i] = $db->select()
                ->from($table, 'value_id')
                ->where('value = ?', $picOrig[$i]['value']);
            $picId[$i] = $db->fetchRow($selectPicId[$i]);
            $picId[$i] = $picId[$i]['value_id']; 
                                                  
        }
         
        // Duplicate main entries of gallery
        foreach ($db->fetchAll($select) as $row) {
            $imagePath = $this->_copyImage($row['value']);
            $data = array(
                'attribute_id' => $attrId,
                'value'        => $imagePath,
            );

            $db->insert($table, $data);
            $valueIdMap[$row['value_id']] = $db->lastInsertId($table);

            $db->insert($tableValueToEntity, array(
                'value_id'    => $valueIdMap[$row['value_id']],
                $entityIdName => $newProductId
            ));
            
            // compare old position of basic, small and thumb with current copied picture
            for ($i = 0; $i < $countPic; $i++) {
                if ($row['value_id'] == $picId[$i])
                {
                      $newPic[$i] = $imagePath;
                }                
            }          
        }

        if (!$valueIdMap) {
            return false;
        }

        // Duplicate per store gallery values
        $tableLabels = $this->resource->getTableName('catalog_product_entity_media_gallery_value');
        $select = $db->select()
            ->from($tableLabels)
            ->where('value_id IN(?)', array_keys($valueIdMap));
/*
        foreach ($db->fetchAll($select) as $row) {
            $data = $row;
            $data['value_id'] = $valueIdMap[$row['value_id']];
            $db->insert($tableLabels, $data);
        }*/
        
        // update basic, small and thumb
        for ($i = 0; $i < $countPic; $i++) {
             $data  = array('value' => $newPic[$i]);
             $where = array(
                'attribute_id = ?'       => $attributePic[$i]->getId(),
                 $entityIdName . ' = ?'  => $newProductId
            );
            $update = $db->update($varcharTable, $data, $where);
            if (!$update && $storeId[$i] != null) {
                $datainsert = array(
                    'attribute_id'   => $attributePic[$i]->getId(),
                    $entityIdName    => $newProductId,
                    'value'          => $newPic[$i],
                    'store_id'       => $storeId[$i],
                );
                $db->insert($varcharTable, $datainsert);
            }            
        }
        return true;
    }
    
    
    protected function _getConfig()
    {
        return $this->_catalogProductMediaConfig;
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param string $file
     * @param bool $forTmp
     * @return string
     */
    protected function getUniqueFileName($file, $forTmp = false)
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            $destFile = $this->fileStorageDb->getUniqueFilename(
                $this->mediaConfig->getBaseMediaUrlAddition(),
                $file
            );
        } else {
            $destinationFile = $forTmp
                ? $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getTmpMediaPath($file))
                : $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getMediaPath($file));
            $destFile = dirname($file) . '/' . FileUploader::getNewFileName($destinationFile);
        }

        return $destFile;
    }
    /**
     * Copy image and return new filename.
     *
     * @param string $file
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _copyImage($file)
    {
        try {
            $destinationFile = $this->getUniqueFileName($file);

            if (!$this->mediaDirectory->isFile($this->mediaConfig->getMediaPath($file))) {
                throw new \Exception();
            }

            if ($this->fileStorageDb->checkDbUsage()) {
                $this->fileStorageDb->copyFile(
                    $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getMediaShortUrl($file)),
                    $this->mediaConfig->getMediaShortUrl($destinationFile)
                );
                $this->mediaDirectory->delete($this->mediaConfig->getMediaPath($destinationFile));
            } else {
                $this->mediaDirectory->copyFile(
                    $this->mediaConfig->getMediaPath($file),
                    $this->mediaConfig->getMediaPath($destinationFile)
                );
            }

            return str_replace('\\', '/', $destinationFile);
        } catch (\Exception $e) {
            $file = $this->mediaConfig->getMediaPath($file);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We couldn\'t copy file %1. Please delete media with non-existing images and try again.', $file)
            );
        }
    }

    protected function _getAttributes(\Magento\Catalog\Model\Product $product, $id)
    {
        // use cached eav config
        $entityTypeId = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();

        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection */
        $collection = $this->_attributeCollectionFactory->create();

        if (!$this->_dImgCodes) {
            $collection->setEntityTypeFilter($entityTypeId);
            $collection->setAttributeSetFilter($product->getAttributeSetId());
            $collection->setFrontendInputTypeFilter('media_image');

            $imgCodes = [];
            foreach ($collection as $attribute) {
                /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $imgCodes[] = $attribute->getAttributeCode();
            }
            $this->_dImgCodes = $imgCodes;
        }

        /** @var \Magento\Catalog\Model\Product  $product */
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')
            ->load($id);

        $collection->resetData();
        $collection->setEntityTypeFilter($entityTypeId);
        $collection->setAttributeSetFilter($product->getAttributeSetId());
        $collection->setFrontendInputTypeFilter('media_image');

        $imgCodes = [];
        foreach ($collection as $attribute) {
            /* @var $attribute \Magento\Eav\Model\Entity\Attribute */
            $imgCodes[] = $attribute->getAttributeCode();
        }

        return array_values(array_intersect($this->_dImgCodes, $imgCodes));
    }
}
