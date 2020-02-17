<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/11/2018
 * Time: 13:37
 */

namespace Forix\Payment\Model\Service;
use Forix\Payment\Model\ServiceAdapterInterface;

interface ServiceInterface
{
    public function getAdapter();
    public function setAdapter(ServiceAdapterInterface $adapter);
}