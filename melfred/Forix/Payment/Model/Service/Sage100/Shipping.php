<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 12/12/2018
 * Time: 10:34
 */

namespace Forix\Payment\Model\Service\Sage100;


class Shipping
{
    /**
     * @var array MAGENTO | SAGE100
     */
    protected static $methodMapping = [
//    ----------UPS---------------------------------
        '03' => 'UPS', //Ground Commercial | UPS-Ground Commercial
        '02' => 'UPS-BLUE', //2nd Day Air | UPS - 2-Day
        '59' => 'UPS-BLUE-AM', //2nd Day Air AM | UPS - 2-Day - AM
        '08' => 'UPS-EXPEDITED', //Worldwide Expedited | UPS Worldwide Expedited
        '07' => 'UPS-EXPRESS', //Worldwide Express | UPS Worldwide Express
        '54' => 'UPS-EXPRESS +', //Worldwide Express Plus | UPS Express Plus
        '12' => 'UPS-ORANGE', //3 Day Select | UPS - 3-Day
        '01' => 'UPS-RED', //Next Day Air | UPS Next Day Air
        '14' => 'UPS-RED-AM', //Next Day Air Early AM | UPS - Next Day - AM
        '11' => 'UPS-STANDARD', //UPS Standard
        '65' => 'UPS-WORLD-SAVER', //UPS Worldwide Saver
//         '' => 'UPS-BLUE-SAT', //UPS 2-day Saturday Delivery
//         '' => 'UPS-EXP + EXT', //UPS Express Plus Extended
//         '' => 'UPS-EXPEDITE-EX', //UPS Expedited - Extended Area
//         '' => 'UPS-EXPRESS-EXT', //UPS Express Extended Area
//         '' => 'UPS-RED-AM-SAT', //UPS-Red Early AM - Saturday
//         '' => 'UPS-RED-PM', //UPS - Next Day - PM
//         '' => 'UPS-RED-SAT', //UPS Next Day Air - Saturday
//         '' => 'UPS-STANDARD', //UPS Std. Ground to Canada
//         '' => 'UPS-WORLD-SAVER', //UPS Worldwide Saver
//         '' => 'US MAIL', //US Mail
//    ----------FEDEX---------------------------------
//        ''  => 'FEDEX_FREIGHT'                                  //Freight
        'FEDEX_2_DAY' => 'FEDX-2DAY',                           //2 Day | FedEx - 2 Day Service
        'FEDEX_2_DAY_AM' => 'FEDX-2DAY-AM',                     //2 Day AM | FedEx 2Day - AM Delivery
        'FEDEX_EXPRESS_SAVER' => 'FEDX-EXP SAVER',              //Express Saver | FedX - Express Saver - 3 Day
        'FIRST_OVERNIGHT' => 'FEDX-FIRST',                      //First Overnight | FedEx First Priority Overnight
        'FEDEX_GROUND' => 'FEDX-GROUND',                        //Ground | FedEx Ground
        'INTERNATIONAL_ECONOMY' => 'FEDX-INTL-ECONO',           //International Economy | FedEx International Economy
        'INTERNATIONAL_PRIORITY' => 'FEDX-INTL-PRIOR',          //International Priority | FedEx International Priority
        'PRIORITY_OVERNIGHT' => 'FEDX-PRIOR',                   //Priority Overnight | Fed Ex Priority Overnight
        'STANDARD_OVERNIGHT' => 'FEDX-STD',                     //Standard Overnight | FedEx Standard Overnight
        'FEDEX_1_DAY_FREIGHT' => 'FEDXFRT-1',                   //1 Day Freight | FedEx Freight - One Day Servic
        'FEDEX_2_DAY_FREIGHT' => 'FEDXFRT-2',                   //2 Day Freight | FedEx Freight - 2-day Service
        'FEDEX_3_DAY_FREIGHT' => 'FEDXFRT-3',                   //3 Day Freight | FedEx Freight - 3-day Service
        'INTERNATIONAL_ECONOMY_FREIGHT' => 'FEDXFRT-INTL-EC',   //Intl Economy Freight | FedEx Freight Intl. Economy
        'INTERNATIONAL_PRIORITY_FREIGHT' => 'FEDXFRT-INTL-PR',  //Intl Priority Freight | FedEx Freight Intl. Priority
//        '' => 'FEDEX LTL AM',       //FedEx LTL - Priority - EarlyAM
//        '' => 'FEDEX LTL INT-E',    //FEDX LTL INT-ECONO
//        '' => 'FEDEX LTL INT-P',    //FEDX LTL INT-PRIOR
//        '' => 'FEDEXFRT-1-SAT',     //FED EX FREIGHT 1-DAY -SAT DEL.
//        '' => 'FEDEXFRT-1SAT',      //FEDX FRT-1SAT
//        '' => 'FEDEXFRT-2-SAT',     //FEDX FRT-2DAY
//        '' => 'FEDX-2DAY-SAT',      //FedEx 2-day, Saturday delivery
        'FEDEX_FREIGHT_ECONOMY' => 'FEDX-LTL ECONO',     //FedEx LTL - Economy - Truck
        'FEDEX_FREIGHT_PRIORITY' => 'FEDX-LTL PRIOR',     //FedEx LTL - Priority - truck
//        '' => 'FEDX-SAT',           //FEDX-SAT PRIOR
//    ---------------------------- OTHER ----------
        'DISTRIBUTOR' => 'W/C'
    ];

    /**
     * @param string|\Magento\Framework\DataObject  $carrier
     * @return string;
     */
    public static function getShippingCode($carrier)
    {
        if(is_string($carrier)) {
            list($carrierCode, $method) = explode('_', $carrier, 2);
        }else{
            $carrierCode = $carrier->getData('carrier_code');
            $method = $carrier->getData('method');
        }
        $method = strtoupper($method);
        if(isset(self::$methodMapping[$method])){
            return self::$methodMapping[$method];
        }
        return strtoupper($carrierCode);
    }
}