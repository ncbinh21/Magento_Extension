<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 02:25
 */

namespace Forix\ProductWizard\Model;


class ProductRelation extends \Magento\Catalog\Model\ProductRender implements \Forix\ProductWizard\Api\Data\ProductRelationInterface
{

    /**
     * Provide is relation required
     *
     * @return int
     * @since 101.1.0
     */
    public function getIsRequired()
    {
        return $this->getData('is_required');
    }

    /**
     * Provide is relation required
     *
     * @param int $isRequired
     * @return void
     * @since 101.1.0
     */
    public function setIsRequired($isRequired)
    {
        $this->setData('is_required', $isRequired);
    }
}