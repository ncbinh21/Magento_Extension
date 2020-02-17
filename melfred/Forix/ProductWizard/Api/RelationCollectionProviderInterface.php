<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 01:06
 */

namespace Forix\ProductWizard\Api;


interface RelationCollectionProviderInterface
{

    /**
     * @param string $sku
     * @param integer $storeId
     * @return \Forix\ProductWizard\Api\Data\ProductRelationInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLinkedProducts($sku, $storeId);

    /**
     * Retrieve BillingCode matching the specified criteria.
     * @param integer $storeId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $sku
     * @return \Forix\ProductWizard\Api\Data\ProductRelationInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $storeId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $sku
    );

}