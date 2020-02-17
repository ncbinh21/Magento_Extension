<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/09/2018
 * Time: 11:13
 */
namespace Forix\ProductWizard\Block\Adminhtml\Group\Item\Option\Edit;

class WizardOptionGridBestOn extends WizardOptionGrid
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'group/item/assign_option_best_on.phtml';
    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                WizardOptionBestOn::class,
                'group.item.options.best.on.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $bestOn = $this->getGroupItemOption()->getBestOn();
        if (!empty($bestOn)) {
            return $this->jsonEncoder->encode(array_combine($bestOn,$bestOn));
        }
        return '{}';
    }
}