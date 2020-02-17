<?php

namespace Forix\NetTermsPayment\Model;

/**
 * Pay In Store payment method model
 */
class Netterms extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'netterms';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
}