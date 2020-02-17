<?php
/**
 * Customer Grid Collection
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Custom\Rewrite\Magento\Sales\Model\ResourceModel\Order\Customer;

class Collection extends \Magento\Customer\Model\ResourceModel\Customer\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addNameToSelect()->addAttributeToSelect(
            'email'
        )->addAttributeToSelect(
            'created_at'
        )->joinAttribute(
            'billing_postcode',
            'customer_address/postcode',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_city',
            'customer_address/city',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_telephone',
            'customer_address/telephone',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_regione',
            'customer_address/region',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_company',
            'customer_address/company',
            'default_billing',
            null,
            'left'
        )->joinAttribute(
            'billing_country_id',
            'customer_address/country_id',
            'default_billing',
            null,
            'left'
        )->joinField(
            'store_name',
            'store',
            'name',
            'store_id=store_id',
            null,
            'left'
        )->joinField(
            'website_name',
            'store_website',
            'name',
            'website_id=website_id',
            null,
            'left'
        )->joinField(
            'company_adv',
            'company_advanced_customer_entity',
            'company_id',
            'customer_id=entity_id',
            null,
            'left'
        )->joinField(
            'company_name',
            'company',
            'company_name',
            'entity_id=company_adv',
            null,
            'left'
        );
        return $this;
    }
}
