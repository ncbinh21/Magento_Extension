<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Block\Instagram;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Yosto\InstagramConnect\Helper\Constant;
use Yosto\InstagramConnect\Helper\InstagramClient;
use Yosto\InstagramConnect\Model\TemplateFactory;

/**
 * Class ImageSlider
 * @package Yosto\InstagramConnect\Block\Instagram
 */
class ShoppingPage extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var InstagramClient
     */
    protected $_instagramConnectHelper;

    /**
     * @var TemplateFactory
     */
    protected $_templateFactory;


    public function __construct(
        Context $context,
        array $data = [],
        InstagramClient $instagramConnectHelper,
        TemplateFactory $templateFactory
    )
    {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        $this->_templateFactory = $templateFactory;
        parent::__construct($context, $data);
    }

    public function getTemplateCode()
    {
        $templateId = $this->_instagramConnectHelper->getShoppingPageTemplate();
        if ($templateId == 0) {
            return false;
        } else {
            $templateModel = $this->_templateFactory->create();
            $currentTemplate = $templateModel->getCollection()->addFieldToFilter(Constant::INSTAGRAM_TEMPLATE_ID, $templateId);
            if ($currentTemplate->count() > 0) {
                return [
                    "pc_code" => $currentTemplate->getFirstItem()->getPcCode(),
                    "mobile_code" => $currentTemplate->getFirstItem()->getMobileCode()
                ];
            } else {
                return false;
            }
        }


    }
    public function getTemplateCodeById($templateId)
    {
        if ($templateId == 0) {
            return false;
        } else {
            $templateModel = $this->_templateFactory->create();
            $currentTemplate = $templateModel->getCollection()->addFieldToFilter(Constant::INSTAGRAM_TEMPLATE_ID, $templateId);
            if ($currentTemplate->count() > 0) {
                return [
                    "pc_code" => $currentTemplate->getFirstItem()->getPcCode(),
                    "mobile_code" => $currentTemplate->getFirstItem()->getMobileCode()
                ];
            } else {
                return false;
            }
        }


    }

    /**
     * @return mixed
     */
    function getBaseUrl(){
        return $this->_instagramConnectHelper->getBaseUrl();
    }

}