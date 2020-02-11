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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier\Composite;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Type extends \Magento\Catalog\Model\Product\Type\Virtual
{
    const TYPE_CREDITPOINTS_FIELD = 'credit_box';
    const TYPE_CREDITPOINTS = 'creditpoints';

    /**
     * @var array
     */
    private $creditOptions = [];

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\CollectionFactory $productOptionCreditCollection,
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository
    ) {
        $this->optionHelper                  = $optionHelper;
        $this->productOptionCreditCollection = $productOptionCreditCollection;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function hasRequiredOptions($product)
    {
        return $this->getCreditOptions($product->getId())->getFirstItem()
            ->getOptionPriceOptions() != Composite::PRICE_TYPE_SINGLE;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isSalable($product)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function canConfigure($product)
    {
        $option = $this->getCreditOptions($product->getId())->getFirstItem();

        $result = true;
        if ($option->getOptionPriceOptions() == Composite::PRICE_TYPE_SINGLE) {
            $result = false;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);

        if ($product->getTypeId() != self::TYPE_CREDITPOINTS) {
            return $options;
        }

        $option = $this->getCreditOptions($product->getId())->getFirstItem();
        if ($option->getOptionPriceOptions() == Composite::PRICE_TYPE_SINGLE) {
            $options['info_buyRequest']['creditOption'] = $option->getId();
        }

        $optionsId = isset($options['info_buyRequest']['creditOptionId'])
            ? $options['info_buyRequest']['creditOptionId']
            : $options['info_buyRequest']['creditOption'];

        $productOption = $this->getCreditOptionsById($product->getId(), $optionsId);
        if (!$productOption) {
            return $options;
        }
        $productOption->setId('creditOption')
            ->setType(\Magento\Catalog\Model\Product\Option::OPTION_GROUP_TEXT);

        $options['info_buyRequest']['creditOptionData'] = $productOption->getData();

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareOptions(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        $options = parent::_prepareOptions($buyRequest, $product, $processMode);

        if (is_object($buyRequest)) {
            $buyRequest = $buyRequest->getData();
        }
        if (!empty($buyRequest['creditOption'])) {
            $options['creditOption']    = $buyRequest['creditOption'];
        }
        if (!empty($buyRequest['creditOptionId'])) {
            $options['creditOptionId'] = $buyRequest['creditOptionId'];
        }

        $isValid = $this->validateOption($product, $buyRequest);

        if (!$isValid) {
            throw new \Magento\Framework\Exception\LocalizedException($this->getSpecifyOptionMessage());
        }

        return $options;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          $buyRequest
     * @return bool
     */
    private function validateOption($product, $buyRequest)
    {
        $optionId = !empty($buyRequest['creditOption']) ? $buyRequest['creditOption'] : 0;
        $optionId = !empty($buyRequest['creditOptionId']) ? $buyRequest['creditOptionId'] : $optionId;

        if ($product->getTypeId() == Composite::PRICE_TYPE_SINGLE) {
            return true;
        }

        $result = ($optionId && $this->getCreditOptionsById($product->getId(), $optionId)) ||
            !(empty($optionId) && $this->hasRequiredOptions($product));

        return $result;
    }

    /**
     * @param int $productId
     * @return \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\Collection
     */
    private function getCreditOptions($productId)
    {
        if (empty($this->creditOptions[$productId])) {
            $this->creditOptions[$productId] = $this->productOptionCreditCollection->create()
                ->addProductFilter($productId);
        }

        return $this->creditOptions[$productId];
    }

    /**
     * @param int $productId
     * @param int $optionId
     * @return \Mirasvit\Credit\Model\ProductOptionCredit
     */
    private function getCreditOptionsById($productId, $optionId)
    {
        return $this->getCreditOptions($productId)->getItemById($optionId);
    }
}
