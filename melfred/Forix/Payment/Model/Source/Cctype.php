<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 29/11/2018
 * Time: 13:46
 */

namespace Forix\Payment\Model\Source;

use Magento\Payment\Model\Source\Cctype as PaymentCctype;

class Cctype extends PaymentCctype
{

    /**
     * @var array
     */
    protected static $cardTypeTranslationMap = [
        'AE'    => 'AMERICAN EXPRESS',
        'DI'    => 'DISCOVER',
        'DC'    => 'DINERS CLUB',
        'JCB'   => 'JCB',
        'MC'    => 'MASTERCARD',
        'VI'    => 'VISA',
        'DN'    => 'DINERS',
    ];

    public static function getPaymentType($ccType){
        if(isset(self::$cardTypeTranslationMap[strtoupper($ccType)])){
            return self::$cardTypeTranslationMap[strtoupper($ccType)];
        }
        return '';
    }

    /**
     * @return string[]
     */
    public function getAllowedTypes()
    {
        return ['VI']; //, 'MC', 'AE', 'DI', 'JCB', 'DN'];
    }
}