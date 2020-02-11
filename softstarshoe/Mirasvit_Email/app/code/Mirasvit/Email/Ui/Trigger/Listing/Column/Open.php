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


class Open extends AbstractColumn
{
    /**
     * {@inheritDoc}
     */
    protected function prepareItem(array $item)
    {
        $value = $item[$this->getName()] ? $item[$this->getName()] : '―';

        //return '<div class="stat-data"><svg class="email__trigger-stat-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M8 3.5c-4.321 0-8 4.393-8 4.393v.161S3.679 12.5 8 12.5s8-4.446 8-4.446v-.161S12.321 3.5 8 3.5zm0 7.179c-1.5 0-2.714-1.196-2.714-2.696S6.5 5.287 8 5.287s2.714 1.196 2.714 2.696S9.5 10.679 8 10.679zm0-3.911c-.679 0-1.25.536-1.25 1.214S7.321 9.196 8 9.196s1.25-.536 1.25-1.214S8.679 6.768 8 6.768z"></path></svg> '.$value.' </div>';
        return '<span class="fa fa-envelope-open"> ' . $value . '</span>';
    }
}
