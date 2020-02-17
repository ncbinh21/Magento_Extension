<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 16:44
 */

namespace Forix\Payment\Model\Service\Sage100;


class PreAuthorizationResult
{
    public $CreditCardTransactionID;
    public $CreditCardAuthorizationNo;
    public $ApprovalIndicator;
    public $AuthorizationDateTime;
    public $Message;
}