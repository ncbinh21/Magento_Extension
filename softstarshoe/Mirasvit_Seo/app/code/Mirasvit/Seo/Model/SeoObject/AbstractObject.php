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

class AbstractObject extends \Magento\Framework\DataObject
{
    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;
    /**
     * @var array
     */
    protected $additional;
    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Mirasvit\Seo\Helper\Parse
     */
    protected $seoParse;

    /**
     * @param \Mirasvit\Seo\Model\Config  $config
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config                  $config,
        \Magento\Framework\Registry                 $registry
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->additional = [
            'category' => [],
            'product' => [],
        ];
        $this->_construct();
    }

    /**
     *
     */
    public function _construct()
    {
    }

    /**
     * @param string $str
     * @return string
     */
    protected function parse($str)
    {
        $storeId = false;
        if ($this->store) {
            $storeId = $this->store->getId();
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->seoParse = $objectManager->get('\Mirasvit\Seo\Helper\Parse');
        $result = $this->seoParse->parse($str, $this->parseObjects, $this->additional, $storeId);

        return $result;
    }

    /**
     * @param string $objectName
     * @param string $variableName
     * @param string $value
     *
     * @return void
     */
    protected function setAdditionalVariable($objectName, $variableName, $value)
    {
        $this->additional[$objectName][$variableName] = $value;
        if (isset($this->parseObjects['product'])) {
            if ($objectName.'_'.$variableName == 'product_final_price_minimal') {
                $this->parseObjects['product']->setData('final_price_minimal', $value);
            }
            if ($objectName.'_'.$variableName == 'product_final_price_range') {
                $this->parseObjects['product']->setData('final_price_range', $value);
            }
        }
    }

    /**
     * @param string $variables
     *
     * @return void
     */
    public function addAdditionalVariables($variables)
    {
        $this->additional = array_merge_recursive($this->additional, $variables);
    }

    /**
     * @return array
     */
    public function getAdditionalVariables()
    {
        return $this->additional;
    }
}
