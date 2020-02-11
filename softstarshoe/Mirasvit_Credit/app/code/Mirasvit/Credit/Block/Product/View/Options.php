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


namespace Mirasvit\Credit\Block\Product\View;

use Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier\Composite;

class Options extends \Magento\Catalog\Block\Product\View\AbstractView
{
    public function __construct(
        \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\CollectionFactory $optionCollection,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        array $data = []
    ) {
        $this->optionCollection = $optionCollection;

        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * @return string
     */
    public function getOptionType()
    {
        return $this->optionCollection->create()->addProductFilter($this->getProduct()->getId())
                    ->addStoreFilter($this->_storeManager->getStore()->getId())
                    ->getFirstItem()
                    ->getOptionPriceOptions();
    }

    /**
     * @return bool
     */
    public function isShowOptions()
    {
        $type = $this->getOptionType();
        if ($type && $type != Composite::PRICE_TYPE_SINGLE) {
            return true;
        }

        return false;
    }
}
