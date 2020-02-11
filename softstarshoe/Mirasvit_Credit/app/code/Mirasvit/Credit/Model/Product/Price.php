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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Credit\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

use Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier\Composite;
use Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\Collection as OptionCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    /**
     * @var array
     */
    private $options = [];
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\CollectionFactory $productOptionCreditCollection,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->request                       = $request;
        $this->productRepository             = $productRepository;
        $this->productOptionCreditCollection = $productOptionCreditCollection;
        $this->optionHelper                  = $optionHelper;

        parent::__construct($ruleFactory, $storeManager, $localeDate, $customerSession, $eventManager, $priceCurrency,
            $groupManagement, $tierPriceFactory, $config);

    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($product)
    {
        $product = $this->calcPrice($product);

        return parent::getPrice($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalPrice($qty, $product)
    {
        if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        $product = $this->calcPrice($product);

        $finalPrice = parent::getFinalPrice($qty, $product);

        $product->setData('final_price', $finalPrice);

        return max(0, $product->getData('final_price'));
    }

    /**
     * @param Product $product
     * @throws \Exception
     *
     * @return Product
     */
    protected function calcPrice($product)
    {
        $options = $this->getOptionsByProduct($product);
        $option  = $options->getFirstItem();

        $creditOption = $product->getCustomOption('option_creditOption');
        if ($creditOption && ($value = $creditOption->getValue())) {
            $id = $value;
            $creditOption = $product->getCustomOption('option_creditOptionId');
            if ($creditOption) {
                $id = $creditOption->getValue();
            }
            $price = 0;
            if ($option  = $options->getItemById($id)) {
                $product = $this->productRepository->getById($product->getId());
                $price = $this->optionHelper->getOptionPrice($option, $value);
                if (!$price) {
                    throw new \Exception('Can not add this product to cart');
                }
            }
            $product->setPrice($price);
        } elseif ($option->getOptionPriceOptions() == Composite::PRICE_TYPE_SINGLE) {
            $product = $this->productRepository->getById($product->getId());
            $product->setPrice($this->optionHelper->getOptionPrice($option));
        }

        return $product;
    }

    /**
     * @param Product $product
     * @return OptionCollection
     */
    private function getOptionsByProduct($product)
    {
        if (!isset($this->options[$product->getId()])) {
            $this->options[$product->getId()] = $this->productOptionCreditCollection->create()
                ->addProductFilter($product->getId());
        }

        return $this->options[$product->getId()];
    }
}
