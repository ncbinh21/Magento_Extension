<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 29/08/2018
 * Time: 15:46
 */

namespace Forix\ProductWizard\Api\Data\ProductRender;
use Magento\Framework\Api\ExtensibleDataInterface;
interface AttributeOptionDataInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     * @since 101.1.0
     */
    public function getAttributeCode();

    /**
     * @return string
     */
    public function getAttributeLabel();

    /**
     * @return string
     */
    public function getAttributeValues();

    /**
     * @param string $code
     * @return void
     */
    public function setAttributeCode($code);

    /**
     * @param string $label
     * @return void
     */
    public function setAttributeLabel($label);

    /**
     * @param string $value
     * @return void
     */
    public function setAttributeValues($value);
}