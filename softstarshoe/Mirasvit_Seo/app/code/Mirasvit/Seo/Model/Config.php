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



namespace Mirasvit\Seo\Model;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

     /**
      * @var \Mirasvit\Seo\Model\Cookie\Cookie
      */
    protected $cookie;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mirasvit\Seo\Model\Cookie\Cookie $cookie,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->cookie = $cookie;
        $this->storeManager = $storeManager;
    }

    const NO_TRAILING_SLASH = 1;
    const TRAILING_SLASH = 2;

    const URL_FORMAT_SHORT = 1;
    const URL_FORMAT_LONG = 2;

    const NOINDEX_NOFOLLOW = 1;
    const NOINDEX_FOLLOW = 2;
    const INDEX_NOFOLLOW = 3;

    const CATEGYRY_RICH_SNIPPETS_PAGE = 1;
    const CATEGYRY_RICH_SNIPPETS_CATEGORY = 2;

    const PRODUCTS_WITH_REVIEWS_NUMBER = 1;
    const REVIEWS_NUMBER = 2;

    const META_TITLE_PAGE_NUMBER_BEGIN = 1;
    const META_TITLE_PAGE_NUMBER_END = 2;
    const META_TITLE_PAGE_NUMBER_BEGIN_FIRST_PAGE = 3;
    const META_TITLE_PAGE_NUMBER_END_FIRST_PAGE = 4;

    const META_DESCRIPTION_PAGE_NUMBER_BEGIN = 1;
    const META_DESCRIPTION_PAGE_NUMBER_END = 2;
    const META_DESCRIPTION_PAGE_NUMBER_BEGIN_FIRST_PAGE = 3;
    const META_DESCRIPTION_PAGE_NUMBER_END_FIRST_PAGE = 4;

    const META_TITLE_MAX_LENGTH = 55;
    const META_DESCRIPTION_MAX_LENGTH = 150;
    const PRODUCT_NAME_MAX_LENGTH = 25;
    const PRODUCT_SHORT_DESCRIPTION_MAX_LENGTH = 90;
    const META_TITLE_INCORRECT_LENGTH = 25;
    const META_DESCRIPTION_INCORRECT_LENGTH = 25;
    const RODUCT_NAME_INCORRECT_LENGTH = 10;
    const PRODUCT_SHORT_DESCRIPTION_INCORRECT_LENGTH = 25;

    const PRODUCT_WEIGHT_RICH_SNIPPETS_KG = 'KGM';
    const PRODUCT_WEIGHT_RICH_SNIPPETS_LB = 'LBR';
    const PRODUCT_WEIGHT_RICH_SNIPPETS_G = 'GRM';

    //seo template rule
    const PRODUCTS_RULE = 1;
    const CATEGORIES_RULE = 2;
    const RESULTS_LAYERED_NAVIGATION_RULE = 3;

    // open graph
    const OPENGRAPH_LOGO_IMAGE = 1;
    const OPENGRAPH_PRODUCT_IMAGE = 2;

    //seo info
    const INFO_IP = 1;
    const INFO_COOKIE = 2;
    const COOKIE_DEL_BUTTON = 'Delete cookie';
    const COOKIE_ADD_BUTTON = 'Add cookie';
    const BYPASS_COOKIE = 'info_bypass_cookie';

    //Description Position
    const BOTTOM_PAGE = 1;
    const UNDER_SHORT_DESCRIPTION = 2;
    const UNDER_FULL_DESCRIPTION = 3;
    const UNDER_PRODUCT_LIST = 4;
    const CUSTOM_TEMPLATE = 5;

    /**
     * @return bool
     */
    public function isAddCanonicalUrl()
    {
        return $this->scopeConfig->getValue('seo/general/is_add_canonical_url');
    }

    /**
     * @return bool
     */
    public function isAddLongestCanonicalProductUrl()
    {
        return $this->scopeConfig->getValue('seo/general/is_longest_canonical_url');
    }


    /**
     * @return int
     */
    public function getAssociatedCanonicalConfigurableProduct()
    {
        return $this->scopeConfig->getValue('seo/general/associated_canonical_configurable_product');
    }

    /**
     * @return int
     */
    public function getAssociatedCanonicalGroupedProduct()
    {
        return $this->scopeConfig->getValue('seo/general/associated_canonical_grouped_product');
    }

    /**
     * @return int
     */
    public function getAssociatedCanonicalBundleProduct()
    {
        return $this->scopeConfig->getValue('seo/general/associated_canonical_bundle_product');
    }

     /**
      * @param int|bool $store
      * @return int
      */
    public function getCrossDomainStore($store = null)
    {
        return $this->scopeConfig->getValue('seo/general/crossdomain',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isPaginatedCanonical()
    {
        return $this->scopeConfig->getValue('seo/general/paginated_canonical');
    }

    /**
     * @return array
     */
    public function getCanonicalUrlIgnorePages()
    {
        $pages = $this->scopeConfig->getValue('seo/general/canonical_url_ignore_pages');
        $pages = explode("\n", trim($pages));
        $pages = array_map('trim', $pages);

        return $pages;
    }

    /**
     * @return array
     */
    public function getNoindexPages()
    {
        $pages = $this->scopeConfig->getValue('seo/general/noindex_pages2');
        if ($pages == '[]' || !$pages) {
            $pages = [];
        } else
        if ($decode = json_decode($pages)) {
            $pages = $decode;
        } else {
            $pages = unserialize($pages);
        }
        $result = [];
        if (is_object($pages)) {
            $pages = (array)$pages;
            foreach ($pages as $key => $value) {
                if (is_object($value)) {
                    $pages[$key] = (array)$value;
                }
            }
        }
        if (is_array($pages)) {
            foreach ($pages as $value) {
                $result[] = new \Magento\Framework\DataObject($value);
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getHttpsNoindexPages()
    {
        return $this->scopeConfig->getValue('seo/general/https_noindex_pages');
    }

    /**
     * @return bool
     */
    public function isPagingPrevNextEnabled()
    {
        return $this->scopeConfig->getValue('seo/general/is_paging_prevnext');
    }

    /**
     * @return bool
     */
    public function isCategoryMetaTagsUsed()
    {
        return $this->scopeConfig->getValue('seo/general/is_category_meta_tags_used');
    }

    /**
     * @return bool
     */
    public function isProductMetaTagsUsed()
    {
        return $this->scopeConfig->getValue('seo/general/is_product_meta_tags_used');
    }

    /**
     * @param string $store
     * @return int
     */
    public function getMetaTitlePageNumber($store)
    {
        return $this->scopeConfig->getValue(
            'seo/extended/meta_title_page_number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getMetaDescriptionPageNumber($store)
    {
        return $this->scopeConfig->getValue(
            'seo/extended/meta_description_page_number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getMetaTitleMaxLength($store)
    {
        return $this->scopeConfig->getValue(
            'seo/extended/meta_title_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getMetaDescriptionMaxLength($store)
    {
        return $this->scopeConfig->getValue(
            'seo/extended/meta_description_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getProductNameMaxLength($store)
    {
        return $this->scopeConfig->getValue(
            'seo/extended/product_name_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getProductShortDescriptionMaxLength($store)
    {
        return $this->scopeConfig->getValue(
            'seo/extended/product_short_description_max_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * SEO URL
     *
     * @return bool
     */
    public function isEnabledSeoUrls()
    {
        return $this->scopeConfig->getValue('seo/url/layered_navigation_friendly_urls');
    }

    /**
     * @return int
     */
    public function getTrailingSlash()
    {
        return $this->scopeConfig->getValue('seo/url/trailing_slash');
    }

    /**
     * @return int
     */
    public function getProductUrlFormat()
    {
        return $this->scopeConfig->getValue('seo/url/product_url_format');
    }

    /**
     * @param string $store
     * @return string
     */
    public function getProductUrlKey($store)
    {
        return $this->scopeConfig->getValue(
            'seo/url/product_url_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|null $storeId
     * @param int|null  $websiteId
     * @return bool
     */
    public function isEnabledRemoveParentCategoryPath($storeId = null, $websiteId = null)
    {
        if ($websiteId) {
            return $this->scopeConfig->getValue(
                'seo/url/use_category_short_url',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
        }

        return $this->scopeConfig->getValue(
            'seo/url/use_category_short_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

    }

    /**
     * IMAGE.
     *
     * @return string
     */
    public function getIsEnableImageFriendlyUrls()
    {
        return $this->scopeConfig->getValue('seo/image/is_enable_image_friendly_urls');
    }

    /**
     * @return string
     */
    public function getImageUrlTemplate()
    {
        return $this->scopeConfig->getValue('seo/image/image_url_template');
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsEnableImageAlt()
    {
        return $this->scopeConfig->getValue('seo/image/is_enable_image_alt');
    }

    /**
     * @return string
     */
    public function getImageAltTemplate()
    {
        return $this->scopeConfig->getValue('seo/image/image_alt_template');
    }

    /**
     * INFO
     *
     * @param null $storeId
     * @return bool
     */
    public function isInfoEnabled($storeId = null)
    {
        if (!$this->_isInfoAllowed()) {
            return false;
        }

        return $this->scopeConfig->getValue(
            'seo/info/info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isShowAltLinkInfo($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'seo/info/alt_link_info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isShowTemplatesRewriteInfo($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'seo/info/templates_rewrite_info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    protected function _isInfoAllowed($storeId = null)
    {
        $info = $this->scopeConfig->getValue(
            'seo/info/info',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (($info == self::INFO_COOKIE)
            && $this->cookie->isCookieExist()) {
                return true;
        } elseif ($info == self::INFO_IP) {
            $ips = $this->scopeConfig->getValue(
                'seo/info/allowed_ip',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            if ($ips == '') {
                return true;
            }
            if (!isset($_SERVER['REMOTE_ADDR'])) {
                return false;
            }
            $ips = explode(',', $ips);
            $ips = array_map('trim', $ips);

            return in_array($_SERVER['REMOTE_ADDR'], $ips);
        }

        return false;
    }

    /**
     * Category
     *
     * @param string $store
     * @return int
     */
    public function getCategoryRichSnippets($store)
    {
        return $this->scopeConfig->getValue(
            'seo_snippets/category_snippets/category_rich_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getRichSnippetsRewiewCount($store)
    {
        return $this->scopeConfig->getValue(
            'seo_snippets/category_snippets/category_rich_snippets_review_count',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Organization
     *
     * @param string $store
     * @return bool
     */
    public function isOrganizationSnippetsEnabled($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/is_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getNameOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/name_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualNameOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_name_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return int
     */
    public function getCountryAddressOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/country_address_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualCountryAddressOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_country_address_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return int
     */
    public function getLocalityAddressOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/locality_address_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualLocalityAddressOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_locality_address_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return string
     */
    public function getPostalCodeOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/postal_code_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualPostalCodeOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_postal_code_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return int
     */
    public function getStreetAddressOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/street_address_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualStreetAddressOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_street_address_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return int
     */
    public function getTelephoneOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/telephone_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualTelephoneOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_telephone_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return string
     */
    public function getManualFaxnumberOrganizationSnippets($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/organization_snippets/manual_faxnumber_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        ));
    }

    /**
     * @param string $store
     * @return int
     */
    public function getEmailOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/email_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getManualEmailOrganizationSnippets($store)
    {
        return $this->scopeConfig->getValue('seo_snippets/organization_snippets/manual_email_organization_snippets',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Rich Snippets Breadcrumbs
     *
     * @param string $store
     * @return int
     */
    public function getBreadcrumbs($store)
    {
        return $this->scopeConfig->getValue(
            'seo_snippets/breadcrumbs_snippets/is_breadcrumbs',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Opengraph
     *
     * @return int
     */
    public function getCategoryOpenGraph()
    {
        return $this->scopeConfig->getValue('seo_snippets/opengraph/is_category_opengraph');
    }

    /**
     * @return bool
     */
    public function isCmsOpenGraphEnabled()
    {
        return $this->scopeConfig->getValue('seo_snippets/opengraph/is_cms_opengraph');
    }


    /**
     * Check if "Use Categories Path for Product URLs" enabled
     *
     * @param int $storeId
     * @return bool
     */
    public function isProductLongUrlEnabled($storeId)
    {
        return $this->scopeConfig->getValue(
                   \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
                   \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                   $storeId
               );
    }
}
