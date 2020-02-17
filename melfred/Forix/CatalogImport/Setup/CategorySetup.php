<?php
/**
 * Catalog entity setup
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\CatalogImport\Setup;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product\Type;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategorySetup extends EavSetup
{
    /**
     * This should be set explicitly
     */
    const CATALOG_PRODUCT_ENTITY_TYPE_ID = 100;


    /**
     * Default entities and attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        return [
            'melfred_catalog_product' => [
                'entity_type_id' => self::CATALOG_PRODUCT_ENTITY_TYPE_ID,
                'entity_model' => \Forix\CatalogImport\Model\Import\Melfredborzall::class,
                'attribute_model' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class,
                'entity_attribute_collection' =>
                    \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection::class,
                'attributes' => []
            ]
        ];
    }
}
