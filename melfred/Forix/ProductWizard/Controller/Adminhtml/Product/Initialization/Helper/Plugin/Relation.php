<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 12/06/2018
 * Time: 12:43
 */

namespace Forix\ProductWizard\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

use Magento\Catalog\Model\Product\Link\Resolver as LinkResolver;
use \Forix\ProductWizard\Model\ResourceModel\Product\Link as ProductWizardLink;
use Magento\Framework\App\ObjectManager;

class Relation
{


    protected $linkResolver;

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterInitialize(\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject, $product)
    {
        $links = $this->getLinkResolver()->getLinks();
        $productLinks = $product->getProductLinks();
        foreach ($productLinks as $productLink) {
            if (ProductWizardLink::LINK_TYPE_RELATION_CODE == $productLink->getLinkType()) {
                foreach ((array)$links[ProductWizardLink::LINK_TYPE_RELATION_CODE] as $linkData) {
                    if ($productLink->getLinkedProductSku() == $linkData['sku']) {
                        $productLink->setAttributeSetId(isset($linkData['attribute_set_id']) ? (int)$linkData['attribute_set_id'] : 0);
                    }
                }
            }
        }
        $product->setProductLinks($productLinks);

        return $product;
    }

    private function getLinkResolver()
    {
        if (!is_object($this->linkResolver)) {
            $this->linkResolver = ObjectManager::getInstance()->get(LinkResolver::class);
        }
        return $this->linkResolver;
    }
}