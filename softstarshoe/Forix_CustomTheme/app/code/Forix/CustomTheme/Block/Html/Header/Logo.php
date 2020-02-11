<?php
/*************************************************
 * *
 *  *
 *  * @copyright Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *  * @author    thao@forixwebdesign.com
 *
 */
namespace Forix\CustomTheme\Block\Html\Header;

use Magento\Theme\Block\Html\Header\Logo as ThemeLogo;
/**
 * Class Logo
 *
 * @package Forix\CustomTheme\Block\Html\Header
 */
class Logo extends ThemeLogo
{

    /**
     * Current template name
     *
     * @var string
     */
    protected $_template = 'Forix_CustomTheme::html/header/logo.phtml';

    /**
     * Retrieve logo image URL
     *
     * @return string
     */
    protected function _getLogoSmallUrl()
    {
        $folderName = \Magento\Config\Model\Config\Backend\Image\Logo::UPLOAD_DIR;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_small_src',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $path = $folderName . '/' . $storeLogoPath;
        $logoUrl = $this->_urlBuilder
                ->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;

        if ($storeLogoPath !== null && $this->_isFile($path)) {
            $url = $logoUrl;
        } elseif ($this->getLogoFile()) {
            $url = $this->getViewFileUrl($this->getLogoFile());
        } else {
            $url = $this->getViewFileUrl('images/logo.svg');
        }
        return $url;
    }

    /**
     *
     */
    public function getLogoSmallSrc()
    {
        if (empty($this->_data['logo_small_src'])) {
            $this->_data['logo_small_src'] = $this->_getLogoSmallUrl();
        }
        return $this->_data['logo_small_src'];

    }
}