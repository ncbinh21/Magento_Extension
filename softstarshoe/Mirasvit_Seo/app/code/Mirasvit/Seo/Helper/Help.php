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



namespace Mirasvit\Seo\Helper;

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
            //General Settings
            'general_is_add_canonical_url' => 'If enabled, will add tag &lt;
                 link rel="canonical" href="http://store.com/"&gt; to META-tags of your store.',
            'general_associated_canonical_configurable_product' => 'If set to "Parent Product": if Simple Product have
                Configurable Product as Parent Product, for simple Product the Canonical Url will
                Configurable Product url.',
            'general_associated_canonical_grouped_product' => 'If set to "Parent Product": if Simple Product have
                Grouped Product as Parent Product, for simple Product the Canonical Url will Grouped Product url.',
            'general_associated_canonical_bundle_product' => 'If set to "Parent Product": if Simple Product have
                Bundle Product as Parent Product, for simple Product the Canonical Url will Bundle Product url.',
            'general_crossdomain' => 'Set default cross-domain canonical URL for multistore configuration.',

            'general_canonical_url_ignore_pages' => 'The list of pages where the Canonical Meta tag will not be added.
                <xmp></xmp>Can be a full action name or a request path. <xmp></xmp>Wildcards are allowed:
                    customer_account_*
                    /customer/account*
                    *customer/account*',

            'general_noindex_pages2' => 'Allows to add headers like "NOINDEX, FOLLOW", "INDEX, NOFOLLOW", "NOINDEX,
                NOFOLLOW" to any page of the store. <xmp></xmp>Can be a full action name or a request path.
                <xmp></xmp>Wildcards allowed. Examples:
                    customer_account_*
                    /customer/account*
                    *customer/account*
                    <xmp></xmp>Examples for layered navigation:
                    filterattribute_(manufacturer)
                    filterattribute_(1level)',

            'general_https_noindex_pages' => 'Allows to add headers like "NOINDEX, FOLLOW", "INDEX, NOFOLLOW",
                "NOINDEX, NOFOLLOW" only for https store.',
            'general_is_alternate_hreflang' => 'Sets "alternate" and "lang" tags for multilingual stores',
            'general_hreflang_locale_code' => 'Identifies the region (in ISO 3166-1 Alpha 2 format) of an alternate URL
                <xmp></xmp>
                <a target="_blank" href="http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2">
                    ISO 3166-1 Alpha 2
                </a> Example: BE',
            'general_is_paging_prevnext' => 'Adds to the head of your products list pages.',
            'general_is_category_meta_tags_used' => 'If set to \'NO\', meta tags of categories will be ignored and
                meta tags of category page will be generated only by template.',
            'general_is_product_meta_tags_used' => 'If set to \'NO\', meta tags of products will be ignored and meta
                tags of product page will be generated only by template.',

            //Extended Settings
            'extended_meta_title_page_number' => 'Add Page Number to Meta Title. Example: "Page 2 | Meta Title Text"',
            'extended_meta_description_page_number' => 'Add Page Number to Meta Description. Example: "Page 2 | Meta
                Description Text"',
            'extended_meta_title_max_length' => 'Crop Meta Title using max length value. Recommended length up to
                55 characters. If you set value less than 25, will be used recommended value 55.<xmp></xmp>
                Leave field empty to disable.',
            'extended_meta_description_max_length' => 'Crop Meta Description using max length value.
                Recommended length up to 150 characters. If you set value less than 25, will be used recommended value
                150.<xmp></xmp>Leave field empty to disable.',

            //Rich Snippets and Opengraph
            'snippets_is_rich_snippets' => 'Adds Rich snippets to product\'s pages. Snippets created using schema.org
                markup schema and microdata format.',
            'snippets_rich_snippets_payment_method' => 'Add snippets of payment methods.',
            'snippets_rich_snippets_delivery_method' => 'Add snippets of delivery(shipping) methods.',
            'snippets_rich_snippets_product_category' => 'Add snippet of Product Category.',
            'snippets_rich_snippets_brand_config' => 'Add an attribute code of the brand. If you want to add a
                few attributes, use the comma separator. For example: country_of_manufacture, manufacturer
                <b>Leave the field empty to not include it to snippets.</b>',
            'snippets_rich_snippets_model_config' => 'Add an attribute code of the model. If you want to add a few
                attributes, use the comma separator. For example: model, car_model
                <b>Leave the field empty to not include it to snippets.</b>',
            'snippets_rich_snippets_color_config' => 'Add an attribute code of the color. If you want to add a few
                attributes, use the comma separator. For example: color, car_color
                <b>Leave the field empty to not include it to snippets.</b>',
            'snippets_rich_snippets_weight_config' => 'If enabled, will add weight snippet. You can set to use
                kilogram or pound.',
            'snippets_rich_snippets_dimensions_config' => 'If enabled, snippets with dimensions will be added (height,
                width or depth have to be configured).',
            'snippets_rich_snippets_dimensional_unit' => 'If use numeric value for dimension you can
                set dimensional unit. For example cm, mm, inch.
                <b>Leave the field empty to not include it to snippets.</b>',
            'snippets_rich_snippets_height_config' => 'Add an attribute code of the height.',
            'snippets_rich_snippets_width_config' => 'Add an attribute code of the width.',
            'snippets_rich_snippets_depth_config' => 'Add an attribute code of the depth.',
            'snippets_rich_snippets_product_condition_config' => 'If enabled, snippets with a product condition
                will be added (Condition Attribute, New Condition Value, Used Condition Value and
                Refurbished Condition Value have to be configured).',
            'snippets_rich_snippets_product_condition_attribute' => 'Add an attribute code of the product condition.',
            'snippets_rich_snippets_product_condition_new' => 'Add value of new product condition.
                Get the value from attribute of the condition.',
            'snippets_rich_snippets_product_condition_used' => 'Add value of used product condition.
                Get the value from attribute of the condition.',
            'snippets_rich_snippets_product_condition_refurbished' => 'Add value of refurbished product condition.
                Get the value from attribute of the condition.',
            'snippets_rich_snippets_product_condition_damaged' => 'Add value of damaged product condition.
                Get the value from attribute of the condition.',
            'snippets_delete_wrong_snippets' => 'If you have snippets which added manually in template,
                it can create conflict with our snippets. This configuration will disable wrong snippets.',
            'snippets_category_rich_snippets' => 'Adds Rich snippets to category\'s pages. Snippets
                are created using schema.org markup schema and microdata format.
                <b>Will show average products rating and minimal price. </b>',
            'snippets_category_rich_snippets_price_text' => 'Text which will be specified in
                Category Rich Snippets, before the minimal price.',
            'snippets_category_rich_snippets_rating_text' => 'Text which will be specified in
                Category Rich Snippets, before the average products rating.',
            'snippets_category_rich_snippets_review_count_text' => 'Text which will be specified in
                Category Rich Snippets, after the review count.',
            'snippets_category_rich_snippets_review_count' => 'Use total number of products with reviews
                or total number of reviews in Category Rich Snippets',
            'snippets_hide_category_rich_snippets' => 'Category Rich Snippets block will be invisible in frontend',
            'snippets_breadcrumbs_separator' => 'Allows to setup the separator for breandcrumb of rich snippets.
                This separator will be shown in the breandcrumb of Google search results.
                <xmp></xmp>Examples: <xmp>/&nbsp;, &nbsp;-&nbsp;, &rarr;</xmp>
                Leave field empty to disable rich snippets breadcrumbs.',
            'snippets_is_breadcrumbs' => 'If you use breadcrumbs different from magento default,
                select "Rich Snippets Breadcrumbs (variant 2)"',
            'snippets_is_organization_snippets' => 'If enabled, adds Organization snippets.',
            'snippets_name_organization_snippets' => 'If set "Add Name from Store Information" name will be added from
                System->General->Store Information->Store Name. If set "Add Name manually" you can set
                Store Name manually.',
            'snippets_manual_name_organization_snippets' => 'Set Store Name manually.',
            'snippets_country_address_organization_snippets' => 'If set "Add Country Address from Store Information"
                Country Address will be added from System->General->Store Information->Country.
                If set "Add Country Address manually" you can set Country Address manually.',
            'snippets_manual_country_address_organization_snippets' => 'Set Country Address manually. For example, USA.
                <xmp></xmp>You can also provide the two-letter
                <br/> <a href="http://en.wikipedia.org/wiki/ISO_3166-1">ISO 3166-1 alpha-2 country code</a>',
            'snippets_manual_locality_address_organization_snippets' => 'The locality. For example, Mountain View.',
            'snippets_manual_postal_code_organization_snippets' => 'The postal code. For example, 94043.',
            'snippets_street_address_organization_snippets' => 'If set "Add Street Address from Store Information"
                Street Address will be added from System->General->Store Information->Store Contact Address.
                If set "Add Street Address manually" you can set Street Address manually.',
            'snippets_manual_street_address_organization_snippets' => 'Set Street Address manually.
                For example, 1600 Amphitheatre Pkwy.',
            'snippets_telephone_organization_snippets' => 'If set "Add Telephone Number from Store Information"
                Street Address will be added from System->General->Store Information->Store Contact Telephone.
                If set "Add Telephone Number manually" you can set Telephone Number manually.',
            'snippets_manual_telephone_organization_snippets' => 'The telephone number.',
            'snippets_manual_faxnumber_organization_snippets' => 'The fax number.',
            'snippets_email_organization_snippets' => 'If set "Add Email from Store Email Addresses"
                Email will be added from System->General->Store Email Addresses->General Contact->Sender Email. If set
                "Add Email manually" you can set Email manually.',
            'snippets_manual_email_organization_snippets' => 'Email address.',
            'snippets_is_opengraph' => 'Adds Facebook Opengraph tags to the head of each product\'s page.',

            //SEO-friendly URLs Settings
            'url_layered_navigation_friendly_urls' => 'If enabled, will make SEO friendly URLs in results
                of Layered Navigation filtering. <b>Will work only with native magento layered navigation.</b>',
            'url_trailing_slash' => 'Manage trailing slash “/” at the end of each store URL.',
            'url_product_url_format' => 'Allows to change URL format for your store.
                <xmp></xmp>You may select between short product URL (like http://store.com/product.html) and
                long product URL (like http://store.com/category1/category2/ product.html).',
            'url_product_url_key' => 'Allows to change a value of product keys by template.
                <b>To apply click "Apply Template For Product URLs"</b>
                <xmp></xmp>You can use all products attributes as variables in format
                <b>[product_(attribute)]</b>
                <xmp></xmp>Example: [product_name] [product_sku] [by {product_manufacturer}] [color {product_color}]',

            'url_apply_template' => 'To ativate a new Product URL Key Template, click the button
                <b>Save config</b> to save SEO general settings. Only after this action press the button
                <b>Apply Template For Product URLs</b> to activate URL template.',

            //Product Images Settings
            'image_is_enable_image_friendly_urls' => 'Will also create duplicate images in "/media/product"
                folder to be reachable via friendly URLs. <b>This feature can use a lot of HDD space.',
            'image_image_url_template' => 'Allows to automatically setup URLs of product images by template.
                <xmp></xmp>You can use variables in this template.
                <xmp></xmp>Example: [product_name] [product_sku] [by {product_manufacturer}] [color {product_color}]',
            'image_is_enable_image_alt' => 'If enabled, will generate alt and title for product images by template.',
            'image_image_alt_template' => 'Template to generate alt and title.
             <xmp></xmp>You can use variables in this template.
             <xmp></xmp>Example: [product_name], [product_sku], [by {product_manufacturer}], [color {product_color}].',

            //Redirect Settings
            'redirect_redirect_error_page_config' => 'Redirect all 404 pages to the added url.
                Can be used "/" as home url. Example: new-url<xmp></xmp>Leave the field empty, to disable.',

            //Info
            'info_info' => 'Seo info. Will be visible in frontend of the store.',
            'info_alt_link_info' => 'Will show links of images with empty or missing alt.',
            'info_templates_rewrite_info' => 'Will show all SEO Templates and SEO Rewrites configured for curent page
                and indicate wich one is applied.',
            'info_allowed_ip' => 'Allowed IPs (comma separated). Leave empty for access from any location.',
        ],
    ];
}
