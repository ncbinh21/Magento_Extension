<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 4/27/2016
 * Time: 12:18 PM
 */

namespace Forix\Base\Model\Payments\Paypal;

class Config extends \Magento\Paypal\Model\Config
{
    /**
     * BN code getter
     *
     * @return string
     */
    public function getBuildNotationCode()
    {
        return 'ForixLLC_SI_MagentoCE';
    }
}