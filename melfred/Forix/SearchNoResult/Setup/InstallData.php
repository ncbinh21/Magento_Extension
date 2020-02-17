<?php
namespace Forix\SearchNoResult\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\BlockFactory as ResourceBlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Store\Model\Store;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var ResourceBlockFactory
     */
    protected $resourceBlockFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param BlockFactory $blockFactory
     * @param ResourceBlockFactory $resourceBlockFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        BlockFactory $blockFactory,
        ResourceBlockFactory $resourceBlockFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->blockFactory = $blockFactory;
        $this->resourceBlockFactory = $resourceBlockFactory;
    }

    /**
     * Install new Swatch entity
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //initial cms blocks
        $blocksData = array(
            array(
                'title' => '[SearchNoResult] Categories List',
                'identifier' => 'noresult_category_list',
                'content' => <<<DATA
    {{widget type="Forix\SearchNoResult\Block\Widget\CategoryList" template="Forix_SearchNoResult::widget/categorylist.phtml" id_path="category/2" level="2"}}
DATA
            ),
        );
        $this->saveCmsBlock($blocksData);
    }

    /**
     * @param $blocks
     * @return \Magento\Cms\Model\Block
     */
    protected function saveCmsBlock($blocks)
    {
        $cmsBlock = $this->blockFactory->create();
        $cmsResourceBlock = $this->resourceBlockFactory->create();
        /** @var \Magento\Cms\Model\Block $cmsBlock */
        foreach ($blocks as $data) {
            $cmsResourceBlock->load($cmsBlock, $data['identifier']);
            if (!$cmsBlock->getData()) {
                $cmsBlock->setData($data);
            } else {
                $cmsBlock->addData($data);
            }
            $cmsBlock->setStoreId([Store::DEFAULT_STORE_ID]);
            $cmsBlock->setIsActive(1);
            $cmsResourceBlock->save($cmsBlock);
        }
        return $cmsBlock;
    }
}