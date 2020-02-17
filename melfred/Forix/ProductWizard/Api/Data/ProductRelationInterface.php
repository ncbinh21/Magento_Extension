<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 02:16
 */

namespace Forix\ProductWizard\Api\Data;

interface ProductRelationInterface extends \Magento\Catalog\Api\Data\ProductRenderInterface
{

    /**
     * Provide is relation required
     *
     * @return int
     * @since 101.1.0
     */
    public function getIsRequired();

    /**
     * Provide is relation required
     *
     * @param string $isRequired
     * @return void
     * @since 101.1.0
     */
    public function setIsRequired($isRequired);
}