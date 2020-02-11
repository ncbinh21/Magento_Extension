<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;

class Copyoptions extends \Amasty\Paction\Model\Command
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    protected $connection;

    /**
     * @var ResourceConnection $resource
     */
    protected $resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ResourceConnection $resource
    ) {
        parent::__construct();
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();

        $this->_type = 'copyoptions';
        $this->_info = [
            'confirm_title'   => 'Copy Custom Options',
            'confirm_message' => 'Are you sure you want to copy custom options?',
            'type'            => $this->_type,
            'label'           => 'Copy Custom Options',
            'fieldLabel'      => 'From',
            'placeholder'     => 'product id'
        ];
    }

    protected $_availableTypes = array('field', 'area', 'file', 'drop_down', 'radio', 'checkbox', 'multiple', 'date', 'date_time', 'time');

    
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
        $srcId = intVal(trim($val));

        if (!$srcId) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid product ID'));
        }

        /** @var \Magento\Catalog\Model\Product  $srcProduct */
        $srcProduct = $this->objectManager->create('Magento\Catalog\Model\Product')->load($srcId);
        $collection = $this->_getCollection($srcProduct);
        if (!count($collection)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a product with custom options'));
        }

        $options = [];
        $countOptions = $collection->getSize();
        foreach ($collection as $option) {
            if (in_array($option->getType(), $this->_availableTypes)) {
                $options[] = $this->_convertToArray($option);
            }
        }  
        
        $num = 0;
        foreach ($ids as $id) {
            if ($srcId == $id)  
                continue;
               
            try {
                /** @var \Magento\Catalog\Model\Product  $product */
                $product = $this->objectManager->create('Magento\Catalog\Model\Product');

                $product->reset()
                    ->load($id)
                    ->setIsMassupdate(true)
                    ->setExcludeUrlRewrite(true);

                $customOptions = [];

                /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory */
                $customOptionFactory = $this->objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory');
                $optionValueFactory = $this->objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory');

                foreach ($options as $option) {

                    /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterface $customOption */
                    $customOption = $customOptionFactory->create(['data' => $option]);
                    $customOption->setProductSku($product->getSku());
                    if (isset($option['values'])) {
                        $values = [];
                        foreach ($option['values'] as $value) {
                            if(!$value['price_type']) $value['price_type'] = 'fixed';
                            $value = $optionValueFactory->create(['data' => $value]);
                            $values[] = $value;
                        }
                        $customOption->setValues($values);
                    }
                    $customOptions[] = $customOption;
                }

                $product->setOptions($customOptions);
                
                $product->save();

                ++$num;
            }
            catch (\Exception $e) {
                $this->_errors[] = __('Can not copy the options to the product ID=%1, the error is: %2',
                    $id, $e->getMessage());
            }
        }
        
        if ($num) {
            return $success = __('Total of %1 products(s) have been successfully updated.', $num);
        }        
        
        return '';
    }
    
    protected function _clean($product, $countOptions)
    {
        $optionsCollection = $this->objectManager->create('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);
        $optionsCollection->getSelect()->order('option_id ASC');
        
        $db    = $this->connection;
        $table = $this->resource->getTableName('catalog_product_option');
        $delete = [];
        $i = 1;
        foreach($optionsCollection as $option) {
            if ($i > $countOptions)
                break;
            $delete[] = $option->getId();
            $i++;
        }
        $db->delete($table, array('option_id IN(?)' => $delete));
        return true;
    }
    
    /**
     * Get options associated with the product as a collection
     *
     * @param int $productId product id
     * @return \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
     */
    protected function _getCollection($product)
    {
        $collection = $this->objectManager->create('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($product);

        return $collection;
    }
    
    /**
     * Converts option object to the array representation
     *
     * @param Mage_Catalog_Model_Product_Option $option otion to convert
     * @return array
     */
    public function _convertToArray($option)
    {
        $commonArgs = array(
            'is_delete',
            'previous_type',
            'previous_group',
            'title',
            'type',
            'is_require',
            'sort_order',
            'values',
        );

        $priceArgs = array(
            'price_type',
            'price',
            'sku',
        );
        
        $txtArgs = array('max_characters');
        
        $fileArgs = array(
            'file_extension',
            'image_size_x',
            'image_size_y'
        );
        
        $type = $option->getType();
        switch ($type) {
            case 'file':
                $optionArgs = array_merge($commonArgs, $priceArgs, $fileArgs);
                break;
            case 'field':
            case 'area':
                $optionArgs = array_merge($commonArgs, $priceArgs, $txtArgs);
                break;
            case 'date':
            case 'date_time':
            case 'time':
                $optionArgs = array_merge($commonArgs, $priceArgs);
                break;
            default :
                $optionArgs = $commonArgs;
        }
        
        
        $optionAsArray = $option->toArray($optionArgs);
        if (in_array($type, array('drop_down', 'radio', 'checkbox', 'multiple'))) {
            $valueArgs = array_merge(array('is_delete', 'title', 'sort_order'), $priceArgs);
            $optionAsArray['values'] = [];
            foreach ($option->getValues() as $value) {
                $optionAsArray['values'][] = $value->toArray($valueArgs);
            }
        }
        
        return $optionAsArray;
    }
}
