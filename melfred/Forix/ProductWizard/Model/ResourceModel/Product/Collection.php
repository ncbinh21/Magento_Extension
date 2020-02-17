<?php
/**
 * Created by Hidro Le.
 * Title: Magento Develop
 * Project: LoveMyBubbles
 * Date: 6/1/17
 * Time: 11:16 AM
 */
namespace Forix\ProductWizard\Model\ResourceModel\Product;
use \Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class Collection extends ProductCollection
{
    const CACHE_TAG = 'forix_wizard_product_collection';
}