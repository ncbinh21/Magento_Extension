<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;


class Changeattributeset extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Entity\Attribute\SetFactory  $attributeSetFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct();
        $this->objectManager = $objectManager;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavConfig = $eavConfig;

        $this->_type = 'changeattributeset';
        $this->_info = [
            'confirm_title'   => 'Change Attribute Set',
            'confirm_message' => 'Are you sure you want to change attribute set?',
            'type'            => 'changeattributeset',
            'label'           => 'Change Attribute Set',
            'fieldLabel'      => 'To',
            'placeholder'     => 'Attribute Set Id'
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
        $success = '';
        $fromId = intVal(trim($val));
        if (!$fromId) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a valid Attribute Group ID'));
        }
        else{
            /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeSet->load($fromId);

            $productEntityId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
            if ($attributeSet->getEntityTypeId() != $productEntityId) {
                throw new \Amasty\Paction\Model\CustomException(__('Provided Attribute set non product Attribute set.'));
            }
        }
        
        $num =  $configurable = 0;
        foreach ($ids as $productId) {
            $product = $this->objectManager->create('Magento\Catalog\Model\Product')
                ->unsetData()
                ->setStoreId($storeId)
                ->load($productId);
            try {
                if ($product->getTypeId() == 'configurable') {
                    $configurable++;
                } else {

                    $product
                        ->setAttributeSetId($fromId)
                        ->setIsMassupdate(true)
                        ->save();
                    //@todo: need delete values of attributes from old attribute set, which absent in new attribute set
                    ++$num;
                }
            } catch (\Exception $e) {
                $this->_errors[] = __('Can not change the attribute set for product ID %1, error is:',
                    $e->getMessage());
            }    
        }

        
        if ($num) {
            $success = __('Total of %1 products(s) have been successfully updated.', $num);
        }

        if ($configurable) {
            $this->_errors[] = __('Total of %1 products(s) have not been updated, the reason: impossibility to change attribute set for configurable product', $configurable);
        }
        
        return $success; 
    }
}
