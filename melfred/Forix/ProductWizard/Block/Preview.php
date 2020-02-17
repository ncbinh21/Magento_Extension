<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 17/09/2018
 * Time: 12:34
 */

namespace Forix\ProductWizard\Block;


class Preview extends \Forix\ProductWizard\Block\Wizard
{

    public function getJsLayout()
    {
        return [
            'Forix_ProductWizard/js/model/wizard-data' => [
                'getProductUrl' => '/relation/products/:sku/:storeId',
                'storeId' => $this->getStoreId(),
                'wizard_url' => $this->getUrl().($this->getCurrentWizard()->getIdentifier()),
                'page_title' => $this->getCurrentWizard()->getTitle(),
                'priceFormat' => $this->getPriceFormat(),
                'required_item_sets' => $this->getCurrentWizard()->getRequiredItemSets(),
                'addToCartAction' => $this->getUrl('productwizard/cart/add')
            ]
        ];
    }
}