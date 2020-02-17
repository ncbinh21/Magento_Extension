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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Config\Backend;

use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Canonical extends ConfigValue
{
    /**
     * @var ConfigResource
     */
    protected $resourceModelConfig;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ConfigResource $resourceModelConfig,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->resourceModelConfig = $resourceModelConfig;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        if ($this->getValue()) {
            $config = $this->resourceModelConfig;
            $config->saveConfig(
                \Magento\Catalog\Helper\Category::XML_PATH_USE_CATEGORY_CANONICAL_TAG,
                0,
                $this->getScope(),
                $this->getScopeId()
            );
            $config->saveConfig(
                \Magento\Catalog\Helper\Product::XML_PATH_USE_PRODUCT_CANONICAL_TAG,
                0,
                $this->getScope(),
                $this->getScopeId()
            );
        }

        return parent::afterSave();
    }
}
