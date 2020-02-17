<?php
namespace Forix\Ajaxscroll\Block;

use Forix\Ajaxscroll\Model\Config\Source\Mode;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Main
 *
 * @package Forix\Ajaxscroll\Block
 */
class Main extends Template
{

    /**@#%
     * Constant XML Path on system.xml
     * @const
     */
    const XML_PATH_AJAX_LOADING_MODE = 'ajax_scroll/general/mode';
    const XML_PATH_HTML_LOADING_BUTTON = 'ajax_scroll/general/html_button';
    const XML_PATH_HTML_LOADING_SPINNER = 'ajax_scroll/general/html_spinner';


    /**
     * @return int
     */
    public function getMode()
    {
        return (int)$this->getConfig(self::XML_PATH_AJAX_LOADING_MODE);
    }

    /**
     * @return bool|string
     */
    public function getHtmlLoadMore()
    {
        if ($html = $this->getConfig(self::XML_PATH_HTML_LOADING_BUTTON)) {
            if (strlen($html) > 0) {
                return $html;
            }
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getHtmlSpinner()
    {
        if ($html = $this->getConfig(self::XML_PATH_HTML_LOADING_SPINNER)) {
            if (strlen($html) > 0) {
                return $html;
            }
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getOptionsToJson()
    {
        $html = [];
        if ($loadMore = $this->getHtmlLoadMore()) {
            $html['load_more'] = $loadMore;
        }

        if ($spinner = $this->getHtmlSpinner()) {
            $html['spinner'] = $spinner;
        }

        return $this->toJson($html);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
