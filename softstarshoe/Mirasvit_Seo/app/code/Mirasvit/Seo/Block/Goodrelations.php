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



namespace Mirasvit\Seo\Block;

/**
 * Блок для вывода Goodrelations на странице продукта.
 */
class Goodrelations extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Payment\Model\Config                                  $config
     * @param \Magento\Tax\Helper\Data                                       $taxData
     * @param \Magento\Framework\Registry                                    $registry
     * @param \Magento\Framework\View\Element\Template\Context               $context
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Payment\Model\Config $config,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->config = $config;
        $this->taxData = $taxData;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Возвращает минимальную цену для группового продукта.
     *
     * @return int
     */
    public function getGroupedMinimalPrice()
    {
        $product = $this->productCollectionFactory->create()
                        ->addMinimalPrice()
                        ->addFieldToFilter('entity_id', $this->getProduct()->getId())
                        ->getFirstItem();

        return $this->taxData->getPrice($product, $product->getMinimalPrice(), true);
    }

    /**
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->context->getStoreManager()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Возвращает доступные платежные методы магазина.
     *
     * @return array
     */
    public function getActivePaymentMethods()
    {
        $payments = $this->config->getActiveMethods();
        $methods = [];
        foreach (array_keys($payments) as $paymentCode) {
            if (strpos($paymentCode, 'paypal') !== false) {
                $methods[] = 'PayPal';
            } elseif (strpos($paymentCode, 'googlecheckout') !== false) {
                $methods[] = 'GoogleCheckout';
            } elseif ($paymentCode == 'ccsave') {
                $methods[] = 'MasterCard';
                $methods[] = 'AmericanExpress';
                $methods[] = 'VISA';
                $methods[] = 'JCB';
                $methods[] = 'Discover';
            }
        }

        return array_unique($methods);
    }
}
