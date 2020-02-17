<?php

/**
 * Forix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Forix.com license that is
 * available through the world-wide-web at this URL:
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Forix
 * @package     Forix_AdvancedAttribute
 * @copyright   Copyright (c) 2012 Forix (http://www.forixwebdesign.com/)
 * @license
 */

namespace Forix\AdvancedAttribute\Block\Adminhtml\Options\Renderer;

/**
 * Image renderer.
 * @category Forix
 * @package  Forix_AdvancedAttribute
 * @module   AdvancedAttribute
 * @author   Forix Developer
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Store manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Image Modal.
     *
     * @var \Forix\AdvancedAttribute\Model\Image
     */
    protected $_imageModel;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Context              $context
     * @param \Magento\Store\Model\StoreManagerInterface  $storeManager
     * @param \Forix\AdvancedAttribute\Model\Image $imageModel
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Forix\AdvancedAttribute\Model\Image $imageModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_imageModel = $imageModel;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if($row->getImage())
            return '<image width="150" height="50" src ="'.$this->_imageModel->getBaseUrl().$row->getImage().'" >';
        else
            return '';
    }
}
