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



namespace Mirasvit\SeoAutolink\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container as GridContainer;

class Link extends GridContainer
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_link';
        $this->_blockGroup = 'Mirasvit_SeoAutolink';
        $this->_addButtonLabel = __('Add New Link');


        $this->buttonList->add('import', [
            'label'   => __('Import Links'),
            'onclick' => "setLocation('" . $this->getUrl('seoautolink/import/index') . "')",
        ]);

        parent::_construct();
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }

}
