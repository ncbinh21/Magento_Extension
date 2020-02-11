<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Addcategory extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
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

        $this->_type = 'addcategory';

        $this->_info = [
            'confirm_title'   => 'Assign Category',
            'confirm_message' => 'Are you sure you want to assign category?',
            'type'            => 'addcategory',
            'label'           => 'Assign Category',
            'fieldLabel'      => 'Category IDs',
            'placeholder'     => 'id1,id2,id3'
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
    public function execute(array $ids, $storeId, $categoryIds)
    {
        $categoryIds = explode(',', $categoryIds);
        if (!is_array($categoryIds)) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide comma separated category IDs'));
        }
        
        if ('replacecategory' == $this->_type) { // remove product(s) from all categories
            $table =  $this->resource->getTableName('catalog_category_product');
            $this->connection->delete($table, ['product_id IN(?)' => $ids]);
            $this->_type = 'addcategory';
        }
        
        $numAffectedCats  = 0;
        $allAffectedProducts = [];
        
        foreach ($categoryIds as $categoryId) {
            if ($categoryId <= 1) {
                $this->_errors[] = __('Unfortunately, Magento2 does not allow to save the category ID=%1', $categoryId);
                continue;
            }
            /** @var $category \Magento\Catalog\Model\Category */
            $category = $this->objectManager->create('Magento\Catalog\Model\Category')
                ->setStoreId($storeId)
                ->load($categoryId);
                
            if (!$category->getId()) {
                $this->_errors[] = __('ID = `%1` has been skipped', $categoryId);
                continue;
            }
            
            $positions = $category->getProductsPosition();
            $currentAffectedProducts = [];
            foreach ($ids as $productId) {
                $has = isset($positions[$productId]);
                
                if ('addcategory' == $this->_type && !$has) { // add only new
                    $positions[$productId] = 0;
                    $currentAffectedProducts[] = $productId;
                } elseif ('removecategory' == $this->_type && $has) { //remove only existing
                    unset($positions[$productId]);
                    $currentAffectedProducts[] = $productId;
                }
            }
            if (count($currentAffectedProducts)) {
                $category->setPostedProducts($positions);
                try {
                    $category->save();
                    ++$numAffectedCats;
                    $allAffectedProducts = array_merge($allAffectedProducts, $currentAffectedProducts);
                    $allAffectedProducts = array_unique($allAffectedProducts);
                } catch (\Exception $e) {
                    $this->_errors[] = __(
                        'Can not handle the category ID=%1, the error is: %2',
                        $categoryId, $e->getMessage()
                    );
                }
            }
        }
        $success = '';
        if ($numAffectedCats) {
            $success = __(
                'Total of %1 category(ies) and %2 product(s) have been successfully updated.',
                $numAffectedCats,
                count($allAffectedProducts)
            );
        } else {
            if (!count($this->_errors)) {
                $success = __('All categories and products have already updated.');
            }
        }
        
        return $success;
    }

}
