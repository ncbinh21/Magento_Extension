<?php

namespace Forix\Custom\Rewrite\Magento\Company\Plugin\Customer\Model\ResourceModel\Grid;

use Magento\Company\Api\Data\CompanyCustomerInterface;
use Magento\Customer\Model\ResourceModel\Grid\Collection;

class CollectionPlugin extends \Magento\Company\Plugin\Customer\Model\ResourceModel\Grid\CollectionPlugin
{
    /**
     * @var string
     */
    private $customerTypeExpressionPattern = '(IF(company_customer.company_id > 0, '
    . 'IF(company_customer.customer_id = company.super_user_id, "%d", "%d"), "%d"))';


    public function beforeLoadWithFilter(
        Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        $subject->getSelect()->joinLeft(
            ['company_customer' => $subject->getTable('company_advanced_customer_entity')],
            'company_customer.customer_id = main_table.entity_id',
            ['company_customer.status']
        );
        $subject->getSelect()->joinLeft(
            ['company' => $subject->getTable('company')],
            'company.entity_id = company_customer.company_id',
            ['company.company_name', 'company.customer_no']
        );
        $subject->getSelect()->columns([
            'customer_type' => new \Zend_Db_Expr($this->prepareCustomerTypeColumnExpression())
        ]);
        return [$printQuery, $logQuery];
    }

    /**
     * Prepare expression for customer type column.
     *
     * @return string
     */
    private function prepareCustomerTypeColumnExpression()
    {
        return sprintf(
            $this->customerTypeExpressionPattern,
            CompanyCustomerInterface::TYPE_COMPANY_ADMIN,
            CompanyCustomerInterface::TYPE_COMPANY_USER,
            CompanyCustomerInterface::TYPE_INDIVIDUAL_USER
        );
    }
}