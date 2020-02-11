<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 07
 * Time: 11:41
 */
namespace Forix\InvoicePrint\Block\Order;

class Invoice extends \Magento\Framework\View\Element\Template
{
    public function toPdf(){
        $this->setTemplate('Forix_InvoicePrint::info/pdf/header.phtml');
        return $this->toHtml();
    }
}