<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 16:53
 */
namespace Forix\AdvanceShipping\Api\Data;

interface ShippingMethodInterface extends \Magento\Quote\Api\Data\ShippingMethodInterface
{

    /**
     * Shipping error message.
     */
    const KEY_METHOD_NOTE = 'method_note';

    /**
     * Returns the shipping method note.
     *
     * @return string Shipping method note.
     */
    public function getMethodNote();

    /**
     * Sets the shipping method note.
     *
     * @param string $methodNote
     * @return $this
     */
    public function setMethodNote($methodNote);
}