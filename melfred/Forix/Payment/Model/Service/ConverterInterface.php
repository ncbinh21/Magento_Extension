<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 19/11/2018
 * Time: 16:29
 */

namespace Forix\Payment\Model\Service;


interface ConverterInterface
{
    /**
     * set data to destination
     * @param \Magento\Framework\DataObject $destination
     * @return \Magento\Framework\DataObject
     */
    public function convertTo(\Magento\Framework\DataObject $destination);

    /**
     * set data from source
     * @param \Magento\Framework\DataObject $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source);
}