<?php namespace Forix\FishPig\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\State;

class UpgradeData implements UpgradeDataInterface {

	protected $_resourceConfig;
	protected $_date;

	public function __construct(
		EavSetupFactory $eavSetupFactory,
		DateTime $dateTime,
		State $appState,
		ConfigInterface $resourceConfig
	)
	{
		$this->eavSetupFactory = $eavSetupFactory;
		$this->_date = $dateTime;
		$this->_resourceConfig = $resourceConfig;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$cmsBlocks = [
				[
					'title'      => '[Blog] Explode',
					'identifier' => 'blog_explode',
					'content'    => '<div class="half-center">
						<h2>EXPLORE</h2>
						<div class="half-center-item w-50">
						<div class="inner h-620">
						<div class="thumbnail"><img title="The Pulpit Rock" src="/media/blog/Group 20.png" alt="The Pulpit Rock" width="430" height="250" /></div>
						<div class="block-text"><a class="title" href="#">OUR STORY</a>
						<p class="is-truncated generated" style="word-wrap: break-word;">Nunc porta sit amet tortor sed interdum. Quisque pulvinar volutpat ex, sit amet suscipit nibh cursus sit amet Nunc porta sit amet tortor sed interdum...</p>
						</div>
						</div>
						</div>
						<div class="half-center-item w-25">
						<div class="inner h-295">
						<div class="thumbnail"><img title="The Pulpit Rock" src="/media/blog/Group 17.png" alt="The Pulpit Rock" width="430" height="250" /></div>
						<div class="block-text"><a class="title" href="#">Quality &amp; Innovation</a>
						<p class="is-truncated generated" style="word-wrap: break-word;">Nunc porta sit amet tortor sed interdum. Quisque pulvinar volutpat ex, sit amet suscipit nibh...</p>
						</div>
						</div>
						</div>
						<div class="half-center-item w-25">
						<div class="inner h-295">
						<div class="thumbnail"><img title="The Pulpit Rock" src="/media/blog/download copy 2.png" alt="The Pulpit Rock" width="430" height="250" /></div>
						<div class="block-text"><a class="title" href="#">Media &amp; Press</a>
						<p class="is-truncated generated" style="word-wrap: break-word;">Nunc porta sit amet tortor sed interdum. Quisque pulvinar volutpat ex, sit amet suscipit nibh...</p>
						</div>
						</div>
						</div>
						<div class="half-center-item w-50">
						<div class="inner h-295">
						<div class="thumbnail"><img title="The Pulpit Rock" src="/media/blog/Group 20.png" alt="The Pulpit Rock" width="430" height="250" /></div>
						<div class="block-text"><a class="title" href="#">Community</a>
						<p class="generated" style="word-wrap: break-word;">Nunc porta sit amet tortor sed interdum. Quisque pulvinar volutpat ex, sit amet suscipit nibh cursus sit amet...</p>
						</div>
						</div>
						</div>
						</div>',
					'is_active'  => 1,
					'create_time' => $this->_date->gmtDate(),
					'update_time' => $this->_date->gmtDate(),
					'stores' => [0]
				]
			];
			$this->_setupCmsBlock($cmsBlocks);
		}

		if (version_compare($context->getVersion(), '1.0.3', '<')) {
			$this->_resourceConfig->saveConfig(
				'wordpress/setup/home_url',
				'http://melfredborzall.local/blog',
				\Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
				\Magento\Store\Model\Store::DEFAULT_STORE_ID
			);
		}

		$setup->endSetup();
	}



	protected function _setupCmsBlock($cmsBlocks)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		foreach ($cmsBlocks as $data) {
			/** @var \Magento\Cms\Model\ResourceModel\Block\Collectiona $collection */
			$collection = $objectManager->create('Magento\Cms\Model\Block')->getCollection();
			$collection->getSelect()->join('cms_block_store', 'main_table.row_id = cms_block_store.row_id', array('store_id' => 'store_id'), null, 'inner');
			$block = $collection->addFieldToFilter('identifier', $data['identifier'])
				->addFieldToFilter('store_id', $data['stores'])
				->getFirstItem();
			if ($block->getData()) {
				$block->setData('content', $data['content'])
					->setData('is_active', $data['is_active'])
					->setData('stores', $data['stores'])
					->setData('title', $data['title'])
					->setData('update_time', $data['update_time'])
					->save();

			} else {
				$objectManager->create('Magento\Cms\Model\Block')->setData($data)->save();
			}
		}
	}
}