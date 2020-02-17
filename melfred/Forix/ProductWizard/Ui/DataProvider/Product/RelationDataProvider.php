<?php

/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 30/05/2018
 * Time: 17:01
 */
namespace Forix\ProductWizard\Ui\DataProvider\Product;

use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;
class RelationDataProvider extends AbstractDataProvider
{
    /**
     * {@inheritdoc
     */
    protected function getLinkType()
    {
        return \Forix\ProductWizard\Model\ResourceModel\Product\Link::LINK_TYPE_RELATION_CODE;
    }
}