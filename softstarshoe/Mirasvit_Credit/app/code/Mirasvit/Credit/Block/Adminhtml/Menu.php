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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['credit']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Credit::credit_transaction',
            'title'    => __('Transactions'),
            'url'      => $this->urlBuilder->getUrl('credit/transaction/index'),
        ])->addItem([
            'resource' => 'Mirasvit_Credit::credit_balance',
            'title'    => __('Customers'),
            'url'      => $this->urlBuilder->getUrl('credit/balance/index'),
        ])->addItem([
            'resource' => 'Mirasvit_Credit::credit_earning',
            'title'    => __('Earning rules'),
            'url'      => $this->urlBuilder->getUrl('credit/earning/index'),
        ])->addItem([
            'resource' => 'Mirasvit_Credit::credit_report',
            'title'    => __('Reports'),
            'url'      => $this->urlBuilder->getUrl('credit/report/view'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Credit::credit_config',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/credit'),
        ]);

        return $this;
    }
}
