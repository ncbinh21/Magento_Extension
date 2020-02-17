<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/06/2018
 * Time: 23:06
 */
namespace Forix\ProductWizard\Model\ProductLink;
use Magento\Catalog\Model\ProductLink\Repository as DefaultRepository;

class Repository extends DefaultRepository
{

    /**
     * @return \Magento\Catalog\Model\Product\LinkTypeProvider
     */
    private function getLinkTypeProvider()
    {
        if (null === $this->linkTypeProvider) {
            $this->linkTypeProvider = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Model\Product\LinkTypeProvider::class);
        }
        return $this->linkTypeProvider;
    }
    
    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array|\Magento\Catalog\Api\Data\ProductLinkInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $output = [];
        $linkTypes = $this->getLinkTypeProvider()->getLinkTypes();
        foreach (array_keys($linkTypes) as $linkTypeName) {
            $collection = $this->entityCollectionProvider->getCollection($product, $linkTypeName);
            foreach ($collection as $item) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->productLinkFactory->create();
                $productLink->setSku($product->getSku())
                    ->setLinkType($linkTypeName)
                    ->setLinkedProductSku($item['sku'])
                    ->setLinkedProductType($item['type'])
                    ->setIsRequired(isset($item['is_required'])?$item['is_required']:0)
                    ->setPosition($item['position']);
                if (isset($item['custom_attributes'])) {
                    $productLinkExtension = $productLink->getExtensionAttributes();
                    if ($productLinkExtension === null) {
                        $productLinkExtension = $this->productLinkExtensionFactory()->create();
                    }
                    foreach ($item['custom_attributes'] as $option) {
                        $name = $option['attribute_code'];
                        $value = $option['value'];
                        $setterName = 'set'.ucfirst($name);
                        // Check if setter exists
                        if (method_exists($productLinkExtension, $setterName)) {
                            call_user_func([$productLinkExtension, $setterName], $value);
                        }
                    }
                    $productLink->setExtensionAttributes($productLinkExtension);
                }
                $output[] = $productLink;
            }
        }
        return $output;
    }
}