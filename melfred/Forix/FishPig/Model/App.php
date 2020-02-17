<?php

namespace Forix\FishPig\Model;

use \FishPig\WordPress\Model\Config;
use \FishPig\WordPress\Model\App\ResourceConnection;
use \FishPig\WordPress\Model\App\Url as WpUrlBuilder;
use \FishPig\WordPress\Model\App\Factory as WpFactory;
use \FishPig\WordPress\Helper\Theme as ThemeHelper;
use Forix\FishPig\Helper\Data;
use Magento\Framework\Encryption\EncryptorInterface;
use FishPig\WordPress\Model\Post\TypeFactory;
use FishPig\WordPress\Model\Term\TaxonomyFactory;

class App extends \FishPig\WordPress\Model\App
{
	/**
	 * @var Data
	 */
	protected $helper;

	protected $enc;

	protected $postTypeFactory;

	protected $taxonomyFactory;

	protected $_request;
	public function __construct(
		Config $config,
		ResourceConnection $resourceConnection,
		WpUrlBuilder $urlBuilder,
		WpFactory $factory,
		ThemeHelper $themeHelper,
		Data $data,
		EncryptorInterface $enc,
		TypeFactory $typeFactory,
		\Magento\Framework\App\Request\Http $request,
		TaxonomyFactory $taxonomyFactory
	)
	{
		parent::__construct($config, $resourceConnection, $urlBuilder, $factory, $themeHelper);
		$this->helper = $data;
		$this->_request = $request;
		$this->enc = $enc;
		$this->postTypeFactory = $typeFactory;
		$this->taxonomyFactory = $taxonomyFactory;
	}

	protected function _init() {
        if (!is_null($this->state)) {
            return $this;
        }
        $this->state = false;

        $fullRequestUri = $this->wpUrlBuilder->getPathInfo($this->_request);
        $blogRoute = $this->wpUrlBuilder->getBlogRoute();
        if ($blogRoute && ($blogRoute !== $fullRequestUri && strpos($fullRequestUri, $blogRoute) !== 0)) {
            return false;
        }

        try {
            // This will load the wp-config.php values
            $this->getWpConfigValue();
            // Define the WP config values globally as WP does
            $this->_defineWpConfigValues();
            // Use the wp-config.php values to connect to the DB
            $this->_initResource();
            // Check that the integration is successful
            $this->_validateIntegration();
            // Plugins can use this to check other things
            $this->performOtherChecks();
            // Mark the state as true. This means all is well
            $this->state = true;
        }
        catch (\Exception $e) {
            $this->exception = $e;
            $this->state = false;
        }

		return $this;

	}

	public function getWpConfigValue($key = null)
	{
		return 'wp_';
	}


	public function getHomepagePageId()
	{
		return false;
	}

	/*
	 * If a page is set as a custom homepage, get it's ID
	 *
	 * @return false|int
	 */
	public function getBlogPageId()
	{
		return false;
	}


	protected function _initResource()
	{
		$pass = $this->helper->getConfigValue('wordpress/setup/password');
		if (!empty($pass)) {
			$pass = $this->enc->decrypt($pass);
		}

		if (!$this->resourceConnection->isConnected()) {
			$this->resourceConnection->setTablePrefix($this->getWpConfigValue('DB_TABLE_PREFIX'))
				->setMappingData(array(
					'before_connect' => $this->getConfig()->getDbTableMapping('before_connect'),
					'after_connect' => $this->getConfig()->getDbTableMapping('after_connect'),
				))
				->connect(array(
						'host'     => $this->helper->getConfigValue('wordpress/setup/host'),
						'dbname'   => $this->helper->getConfigValue('wordpress/setup/db_name'),
						'username' => $this->helper->getConfigValue('wordpress/setup/username'),
						'password' => $pass,
						'active'   => '1',
					)
				);
		}

		return $this;
	}


	protected function _defineWpConfigValues()
	{
		return $this;
	}


	public function getAllPostTypes()
	{
		$postTypes = array();

		$postTypes = array(
			'post' => $this->postTypeFactory->create(),
			'page' => $this->postTypeFactory->create()
		);

		$postTypes['post']->addData(array(
			'post_type' => 'post',
			'rewrite' => array('slug' => $this->getConfig()->getOption('permalink_structure')),
			'taxonomies' => array('category', 'post_tag'),
			'_builtin' => true,
		));

		$postTypes['page']->addData(array(
			'post_type' => 'page',
			'rewrite' => array('slug' => '%postname%/'),
			'hierarchical' => true,
			'taxonomies' => array(),
			'_builtin' => true,
		));

		return $postTypes;
	}


	public function getAllTaxonomies()
	{
		$this->_init();

		$blogPrefix = $this->isMultisite() && $this->getConfig()->getBlogId() === 1;

		$bases = array(
			'category' => $this->getConfig()->getOption('category_base') ? $this->getConfig()->getOption('category_base') : 'category',
			'post_tag' => $this->getConfig()->getOption('tag_base') ? $this->getConfig()->getOption('tag_base') : 'tag',
		);

		foreach($bases as $baseType => $base) {
			if ($blogPrefix && $base && strpos($base, '/blog') === 0) {
				$bases[$baseType] = substr($base, strlen('/blog'));
			}
		}

		$taxonomies = array(
			'category' => $this->taxonomyFactory->create(),
			'post_tag' => $this->taxonomyFactory->create()
		);

		$taxonomies['category']->addData(array(
			'type' => 'category',
			'taxonomy_type' => 'category',
			'labels' => array(
				'name' => 'Categories',
				'singular_name' => 'Category',
			),
			'public' => true,
			'hierarchical' => true,
			'rewrite' => array(
				'hierarchical' => true,
				'slug' => $bases['category'],
			),
			'_builtin' => true,
		));

		$taxonomies['post_tag']->addData(array(
			'type' => 'post_tag',
			'taxonomy_type' => 'post_tag',
			'labels' => array(
				'name' => 'Tags',
				'singular_name' => 'Tag',
			),
			'public' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => $bases['post_tag'],
			),
			'_builtin' => true,
		));

		return $taxonomies;
	}


}