<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/09/2018
 * Time: 11:13
 */
namespace Forix\ProductWizard\Block\Adminhtml\Group\Item\Option\Edit;

class WizardOptionGridDependOn extends WizardOptionGrid
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'group/item/assign_option_depend_on.phtml';
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
                WizardOptionDependOn::class,
                'group.item.options.depend.on.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $dependOn = $this->getGroupItemOption()->getDependOn();
        if (!empty($dependOn)) {
            return $this->jsonEncoder->encode(array_combine($dependOn,$dependOn));
        }
        return '{}';
    }

}