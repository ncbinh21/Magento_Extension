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



namespace Mirasvit\Credit\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Url;
use Magento\Framework\DataObject;

/**
 * Class Mirasvit_Credit_Helper_Renderer.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Renderer extends \Magento\Backend\Block\Widget
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Url
     */
    protected $backendUrl;

    public function __construct(
        Url $backendUrl,
        Context $context,
        $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->context      = $context;
        $this->backendUrl   = $backendUrl;

        parent::__construct($context, $data);
    }

    /**
     * @param float      $value
     * @param DataObject $row
     * @param DataObject $column
     * @param bool       $isExport
     * @return string
     */
    public function amountDelta($value, $row, $column, $isExport)
    {
        if ($isExport) {
            $column->setCurrencyCode('');

            return $value;
        }
        if ($row->getData($column->getIndex()) > 0) {
            return '<span style="color:#0a0">+' . $value . '</span>';
        } else {
            return '<span style="color:#f00">' . $value . '</span>';
        }
    }

    /**
     * @param float      $value
     * @param DataObject $row
     * @param DataObject $column
     * @param bool       $isExport
     * @return string
     */
    public function amount($value, $row, $column, $isExport)
    {
        if ($row->getData($column->getIndex()) == 0 && !$isExport) {
            return '—';
        }

        return $value;
    }

    /**
     * @param float      $value
     * @param DataObject $row
     * @param DataObject $column
     * @param bool       $isExport
     * @return string
     */
    public function transactionMessage($value, $row, $column, $isExport)
    {
        if ($isExport) {
            return $value;
        }

        return $row->getBackendMessage();
    }

    /**
     * @param float                         $value
     * @param \Magento\Framework\DataObject $row
     * @param \Magento\Framework\DataObject $column
     * @return string
     */
    public function websiteCode($value, $row, $column)
    {
        return $this->storeManager->getWebsite($value)->getCode();
    }

    /**
     * @param float      $value
     * @param DataObject $row
     * @param DataObject $column
     * @param bool       $isExport
     * @return string
     */
    public function customerName($value, $row, $column, $isExport)
    {
        if ($isExport) {
            return $value;
        }

        $url = $this->backendUrl->getUrl('customer/index/edit', ['id' => $row->getCustomerId()]);

        return "<a href='$url'>$value</a>";
    }
}
