<?php

namespace Forix\AdvanceShipping\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $_blockRepository;
    protected $_blockFactory;
    public function __construct(
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Cms\Api\Data\BlockInterfaceFactory $blockFactory
    )
    {
        $this->_blockRepository = $blockRepository;
        $this->_blockFactory = $blockFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(),'1.1.0','<')) {
            /**
             * @var $blockDataModel \Magento\Cms\Api\Data\BlockInterface
             */
            $blockDataModel = $this->_blockFactory->create();
            $blockDataModel->setTitle('[All][Checkout] Shipping Note');
            $blockDataModel->setIdentifier('all_checkout_shipping_note');
            $blockDataModel->setContent('<strong>Note:</strong> You may be charged more than estimated shipping because final shipping costs are calculated after checkout.');
            $blockDataModel->setStoreId([0]);
            $this->_blockRepository->save($blockDataModel);
            $blockPopupModel = $this->_blockFactory->create();
            $blockPopupModel->setTitle('[All][Checkout] Shipping Note Popup Content');
            $blockPopupModel->setIdentifier('all_checkout_shipping_note_popup');
            $blockPopupModel->setContent('Shipping Note Popup');
            $blockPopupModel->setStoreId([0]);
            $this->_blockRepository->save($blockPopupModel);
        }
        if (version_compare($context->getVersion(),'1.1.1','<')) {
            $blockDataModel = $this->_blockFactory->create();
            $blockDataModel->setTitle('[All][Checkout] Shipping Heavy Item Note');
            $blockDataModel->setIdentifier('all_checkout_shipping_heavy_item_note');
            $blockDataModel->setContent('<strong>Note:</strong> Note that you\'ll need to have a forklift to unload the heavy items.');
            $blockDataModel->setStoreId([0]);
            $this->_blockRepository->save($blockDataModel);
        }
		$setup->endSetup();
	}
}