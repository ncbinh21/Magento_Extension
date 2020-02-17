<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 15:37
 */

namespace Forix\Payment\Model\Service\Sage100;

/**
 * Class Logon
 * The user name and password to be used for the operation
 * @package Forix\Payment\Model\Service\Sage100
 */
class Logon
{
    public $Username;
    public $Password;

    public function __construct(
        $Username, $Password
    )
    {
        $this->Username = $Username;
        $this->Password = $Password;
    }
}