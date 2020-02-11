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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\SeoObject;

class Producturl extends \Mirasvit\Seo\Model\SeoObject\AbstractObject
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     * @var array
     */
    protected $parseObjects = [];

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->init();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        $this->parseObjects['product'] = $this->product;

        return $this;
    }

    /**
     * @param string $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->parseObjects['store'] = $store;
        $this->_store = $store;

        return $this;
    }

    /**
     *
     */
    protected function init()
    {
    }

    /**
     * @param string $template
     * @return string
     */
    public function parse($template)
    {
        return parent::parse($template);
    }
}
