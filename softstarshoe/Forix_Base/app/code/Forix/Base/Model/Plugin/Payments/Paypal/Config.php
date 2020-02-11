<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 5/13/2016
 * Time: 4:46 PM
 */

namespace Forix\Base\Model\Plugin\Payments\Paypal;

class Config
{
    /**
     * Always return Forix BN code
     * 
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetBuildNotationCode($subject, $result)
    {
        return 'ForixLLC_SI_MagentoCE';
    }
}