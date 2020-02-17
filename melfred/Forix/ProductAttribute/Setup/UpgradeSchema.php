<?php
namespace Forix\ProductAttribute\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

	protected $categorySetupFactory;

	public function __construct(
		\Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
	)
	{
		$this->categorySetupFactory = $categorySetupFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '3.8.6', '<')) {
			$categorySetup = $this->categorySetupFactory->create();
			$entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
			$categorySetup->removeAttribute($entityTypeId, 'mb_badge_heavy');
		}

		$setup->endSetup();
	}


}
