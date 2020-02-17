<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: Melfredborzall
 * Date: 11/06/2018
 * Time: 12:01
 */
namespace Forix\Product\Observer\Backend;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CatalogProductSaveAfterObserver implements ObserverInterface
{
    protected $request;
    protected $productTypeFactory;
    protected $matrixHelper;
    public function __construct(
        RequestInterface $request,
        \Forix\Product\Helper\Product\Matrix $matrixHelper,
        \Forix\Product\Model\ProductTypeFactory $productTypeFactory
    )
    {
        $this->request = $request;
        $this->matrixHelper = $matrixHelper;
        $this->productTypeFactory = $productTypeFactory;
    }

    
    
    /**
     * Catalog Product After Save (change status process)
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() === ConfigurableProduct::TYPE_CODE) {
            $configurableMatrix = $this->request->getParam('configurable-matrix-serialized', "[]");
            if (isset($configurableMatrix) && $configurableMatrix != "") {
                $configurableMatrix = json_decode($configurableMatrix, true);

                $data = [];
                foreach ($configurableMatrix as $matrix) {
                    if (!$product->getData('super_link_data')) {
                        $linked = $this->matrixHelper->getLinkedData($product->getId());
                        $product->setData('super_link_data', $linked);
                    }
                    $linked = $product->getData('super_link_data');
                    foreach ($linked as $item) {
                        if ($item['product_id'] == $matrix['id'] && $product->getId() == $item['parent_id']) {
                            if (isset($matrix['recommend_sku']) && $matrix['recommend_sku']) {
                                $item['recommend_sku'] = $matrix['recommend_sku'];
                            } else {
                                $item['recommend_sku'] = \Forix\Product\Model\Product\LinkOptions\Recommend::STATUS_NOT_RECOMMEND;
                            }
                            $data[] = $item;
                        }
                    }
                }
                if (count($data)) {
                    /**
                     * @var $productType \Forix\Product\Model\ProductType
                     */
                    $productType = $this->productTypeFactory->create();
                    $productType->getResource()->getConnection()->insertOnDuplicate($productType->getResource()->getMainTable(), $data, ['recommend_sku']);
                }
            }
        }
    }
}