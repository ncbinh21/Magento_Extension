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
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\SeoObject;

class Pager extends \Magento\Framework\DataObject
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $page = $this->request->getParam('p');
        if ($page > 1) {
            $this->setPage(__('Page %1', $page));
        }
    }
}
