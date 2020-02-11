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


class Click extends AbstractColumn
{
    /**
     * {@inheritDoc}
     */
    protected function prepareItem(array $item)
    {
        $value = $item[$this->getName()] ? $item[$this->getName()] : '―';

        //return '<div class="stat-data"><svg class="email__trigger-stat-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path d="M12.321 8.911l-3.5.875 1.839 4.179-2.143.946-1.839-4.196-3 2.018V1.09z"></path></svg> '.$value.' </div>';
        return '<span class="fa fa-mouse-pointer"> ' . $value . '</span>';
    }
}
