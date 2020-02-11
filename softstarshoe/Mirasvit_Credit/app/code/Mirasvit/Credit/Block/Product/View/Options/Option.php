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


namespace Mirasvit\Credit\Block\Product\View\Options;

class Option extends \Magento\Bundle\Block\Catalog\Product\Price
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\Collection $optionCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Tax\Helper\Data $taxData,
        array $data = []
    ) {
        $this->optionHelper     = $optionHelper;
        $this->optionCollection = $optionCollection;

        parent::__construct($context, $jsonEncoder, $catalogData, $registry,
            $string, $mathRandom, $cartHelper, $taxData, $data);
    }

    /**
     * @return \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\Collection
     */
    protected function getOptionsCollection()
    {
        return $this->optionCollection->addProductFilter($this->getProduct()->getId())
            ->addStoreFilter($this->_storeManager->getStore()->getId());
    }

    /**
     * @return array
     */
    public function getOptionData()
    {
        $collection = $this->getOptionsCollection();

        $data = [];
        /** @var \Mirasvit\Credit\Api\Data\ProductOptionCreditInterface $option */
        foreach ($collection as $option) {
            $data[] = [
                'id'        => $option->getId(),
                'type'      => $option->getOptionPriceType(),
                'credits'   => $option->getOptionCredits(),
                'min'       => $option->getOptionMinCredits(),
                'max'       => $option->getOptionMaxCredits(),
                'rangeRate' => $option->getOptionPrice(),
                'price'     => $this->optionHelper->getOptionPrice($option),
            ];
        }

        return $data;
    }
}