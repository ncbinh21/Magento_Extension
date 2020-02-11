<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Trigger\Listing\Column;


class Email extends AbstractColumn
{
    /**
     * {@inheritDoc}
     */
    protected function prepareItem(array $item)
    {
        $value = $item[$this->getName()] ? $item[$this->getName()] : '―';

        //return '<div class="stat-data"><svg class="email__trigger-stat-icon" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M13.836 14.005H.5s.377-4.236 5.995-5.17c-.915-.915-1.67-2.028-1.67-3.822 0-1.185.055-4.003 3.214-4.003 3.069 0 3.212 2.818 3.212 4.003 0 1.794-.771 2.98-1.669 3.823 5.762 1.346 5.995 5.043 5.905 5.169" fill-rule="evenodd"></path></svg> '.$value.' </div>';
        return '<span class="fa fa-send"> ' . $value . '</span>';
    }
}
