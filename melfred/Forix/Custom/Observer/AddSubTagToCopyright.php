<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 04/09/2018
 * Time: 10:40
 */

namespace Forix\Custom\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddSubTagToCopyright implements ObserverInterface
{
    protected $_config;
    protected $_htmlSubTagHelper;
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Forix\Custom\Helper\HtmlSubTagHelper $htmlSubTagHelper
    ){
        $this->_config = $config;
        $this->_htmlSubTagHelper = $htmlSubTagHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_config->isEnabled() || $this->_config->getType() != \Magento\PageCache\Model\Config::BUILT_IN) {
            /**
             * @var $response \Magento\Framework\App\Response\Http
             */
            $response = $observer->getResponse();
            $body = $response->getContent();
            $afterAddSubtag = $this->_htmlSubTagHelper->addSubTag($body);
            $response->setContent($afterAddSubtag);
        }
    }
}