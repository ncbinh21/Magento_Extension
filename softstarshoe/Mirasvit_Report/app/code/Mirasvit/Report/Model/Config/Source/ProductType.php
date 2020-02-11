<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductTypeListInterface;

class ProductType
{
    /**
     * @var ProductTypeListInterface
     */
    protected $productTypeList;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ProductTypeListInterface $productTypeList
     * @param ScopeConfigInterface     $scopeConfig
     */
    public function __construct(
        ProductTypeListInterface $productTypeList,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->productTypeList = $productTypeList;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        $result = [];
        $types = $this->productTypeList->getProductTypes();
        foreach ($types as $type) {
            $result[$type->getName()] = $type->getLabel();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        $types = $this->productTypeList->getProductTypes();

        foreach ($types as $type) {
            $result[] = [
                'label' => $type->getLabel(),
                'value' => $type->getName(),
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
