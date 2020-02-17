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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Helper;

/**
 * @SuppressWarnings(PHPMD)
 */
class Help extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @var array
     */
    protected $help = [
        'system' => [

            //Frontend Sitemap Settings
            'frontend_sitemap_base_url' => 'Base path to the frontend sitemap page.
                <b>To apply store CSS settings, this should not be the folder containing sitemap.xml</b>',
            'frontend_sitemap_meta_title' => 'Meta title of sitemap HTML page.',
            'frontend_sitemap_meta_keywords' => 'Meta keywords of sitemap HTML page.',
            'frontend_sitemap_meta_description' => 'Meta description of sitemap HTML page.',
            'frontend_sitemap_h1' => 'H1 tag of sitemap HTML page.',
            'frontend_is_show_products' =>
                'If enabled, a list of all active catalog products will be included into the frontend sitemap.',
            'frontend_is_show_cms_pages' =>
                'If enabled, a list of CMS pages will be included into the frontend sitemap.',
            'frontend_ignore_cms_pages' =>
                'Defines a list of CMS pages, which will not be displayed in the frontend sitemap.',
            'frontend_is_show_stores' => 'If enabled, a list of Store Views will be displayed in the frontend sitemap.',
            'frontend_additional_links' =>
                'Defines a comma-separated list of links which will be added to the frontend sitemap.
                <xmp></xmp>Example:
                /promotions/, Our Promotions
                /customer/account/, Customer Account',
            'frontend_exclude_links' =>
                'Defines a list of patterns for links that will be excluded from the sitemap.
                Can be a full action name or a request path.
                <xmp></xmp>Example:
                    /furniture.html
                    /22-syncmaster-lcd-monitor.html
                    /universal-camera*
                    *memory*
                    *laptops.html
                    /electronics/computers/mon*',
            'frontend_links_limit' => 'If not empty, the frontend sitemap will be splitted by several pages.',

            //Google Sitemap Extended Settings
            'google_is_add_product_images' =>
                'If enabled, links to product images will be included in Google sitemap.',
            'google_is_enable_image_friendly_urls' =>
                'If enabled, will make SEO friendly URLs for images of products in the sitemap.',
            'google_image_url_template' =>
                'Allows to automatically setup URLs of product images by template in the sitemap. In the template
                you can use all product attributes as variables in format [product_attribute]
                <xmp></xmp>Example: [product_name] [product_sku] [by {product_manufacturer}] [color {product_color}]',
            'google_image_size_width' => 'Width for sitemap images in pixels.',
            'google_image_size_height' => 'Height for sitemap images in pixels.',
            'google_is_add_product_tags' => 'If enabled, links to tags will be included is Google sitemap.',
            'google_product_tags_changefreq' => 'Defines frequency of possible tag\'s page changes.',
            'google_product_tags_priority' =>
                'Defines priority of tag\'s link for search engine. Relative within your site. Range [0.0 - 1.0]',
            'google_link_changefreq' => 'Defines frequency of possible link\'s page changes.',
            'google_link_priority' =>
                'Defines priority of link for search engine. Relative within your site. Range [0.0 - 1.0]',
            'google_split_size' =>
                'Defines a maximum size of sitemap file. If sitemap size is more than this parameter,
                 sitemap will be splitted on several files and Sitemap XML Index file will be added.
                 <xmp></xmp>Max 51200. Leave it empty to disable this option.',
            'google_max_links' =>
                'Defines a maximum number of links per file. If sitemap has more than this parameter,
                    sitemap will be splitted on several files and Sitemap XML Index file will be added.
                    <xmp></xmp>Max 50000. Leave it empty to disable this option.',
        ],
    ];

    /************************/
}
