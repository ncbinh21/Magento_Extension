<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Plugin\Block;

use Magento\Framework\Data\Tree\Node;

/**
 *
 * Class Topmenu
 * @package Yosto\InstagramConnect\Plugin\Block
 */
class Topmenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Yosto\InstagramConnect\Helper\InstagramConnectHelper
     */
    protected $_instagramConnectHelper;

    /**
     * Topmenu constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Yosto\InstagramConnect\Helper\InstagramConnectHelper $instagramConnectHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Yosto\InstagramConnect\Helper\InstagramConnectHelper $instagramConnectHelper,
        array $data = []
    )
    {
        $this->_request = $request;
        $this->_instagramConnectHelper = $instagramConnectHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject
    )
    {
        if ($this->_instagramConnectHelper->getShoppingPageLinkMenu() == true) {
            $node = new Node(
                $this->getNodeAsArray(),
                'id',
                $subject->getMenu()->getTree(),
                $subject->getMenu()
            );
            $subject->getMenu()->addChild($node);
        }
    }

    /**
     * @return array
     */
    protected function getNodeAsArray()
    {
        $requestUri = $this->_request->getRequestUri();
        //$linkLabel= 'Shop by Instagramxxx';
        if ($this->_instagramConnectHelper->getShoppingPageLinkLabel() != null && $this->_instagramConnectHelper->getShoppingPageLinkLabel() != '') {
            $linkLabel = $this->_instagramConnectHelper->getShoppingPageLinkLabel();
        } else {
            $linkLabel = 'Shop by Instagram';
        }
        if (false !== strpos($requestUri, '/instagram/shopping/index')) {
            $isActive = true;
        } else {
            $isActive = false;
        }
        return [
            'name' => _($linkLabel),
            'id' => 'instagram-shopping-page',
            'url' => '/instagram/shopping/index',
            'has_active' => false,
            'is_active' => $isActive
        ];
    }
}