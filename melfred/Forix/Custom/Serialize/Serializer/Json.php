<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 14/12/2018
 * Time: 01:56
 */

namespace Forix\Custom\Serialize\Serializer;


class Json extends \Magento\Framework\Serialize\Serializer\Json
{

    /**
     * {@inheritDoc}
     * @since 100.2.0
     */
    public function unserialize($string)
    {
        if($string = trim($string)){
            return json_decode($string, true);
        }
        return $string;
    }
}