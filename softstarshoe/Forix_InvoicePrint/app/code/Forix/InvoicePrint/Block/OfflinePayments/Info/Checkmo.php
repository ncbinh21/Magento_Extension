<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 06
 * Time: 17:22
 */
namespace Forix\InvoicePrint\Block\OfflinePayments\Info;

class Checkmo extends \Magento\OfflinePayments\Block\Info\Checkmo
{
    public function toPdf()
    {
        $this->setTemplate('Forix_InvoicePrint::info/pdf/checkmo.phtml');
        return $this->toHtml();
    }
}