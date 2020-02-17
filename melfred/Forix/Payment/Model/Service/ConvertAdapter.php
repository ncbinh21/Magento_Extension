<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 19/11/2018
 * Time: 15:44
 */

namespace Forix\Payment\Model\Service;


use Forix\Payment\Model\Service\ConverterInterface;

class ConvertAdapter implements ConverterAdapterInterface
{
    /**
     * @param $source
     * @param $destination
     * @return \Magento\Framework\DataObject|mixed
     */
    public function convert($source, $destination)
    {
        if ($source instanceof ConverterInterface) {
            return $source->convertTo($destination);
        }
        /**
         * @var $destination ConverterInterface
         */
        return $destination->convertFrom($source);
    }
}