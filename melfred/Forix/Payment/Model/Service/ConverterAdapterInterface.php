<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 19/11/2018
 * Time: 15:24
 */

namespace Forix\Payment\Model\Service;

use \Forix\Payment\Model\Service\ConverterInterface;

interface ConverterAdapterInterface
{
    /**
     * @param $source
     * @param $destination
     * @return mixed
     */
    public function convert($source, $destination);

}