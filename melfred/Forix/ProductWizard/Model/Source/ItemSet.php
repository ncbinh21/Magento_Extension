<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 03/07/2018
 * Time: 14:58
 */

namespace Forix\ProductWizard\Model\Source;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

use \Forix\ProductWizard\Helper\Data as HelperData;

class ItemSet implements SourceInterface, OptionSourceInterface
{
    /**
     * @var
     */
    protected $_coreRegistry;
    protected $wizardRepository;

    public function __construct(
        \Forix\ProductWizard\Model\WizardRepository $wizardRepository,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->wizardRepository = $wizardRepository;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * @return \Forix\ProductWizard\Model\GroupItemOption
     */
    protected function getCurrentOption()
    {
        return $this->_coreRegistry->registry('forix_productwizard_group_item_option');
    }


    public function getOptionArray()
    {
        try {
            $currentOption = $this->getCurrentOption();
            if ($currentOption && $wizardId = $currentOption->getWizardId()) {
                $wizard = $this->wizardRepository->getById($wizardId);//
                $itemSets = $wizard->getRequiredItemSets();
                $options = [];
                foreach ($itemSets as $itemSet) {
                    $itemSetCustom = HelperData::cleanCssIdentifier($itemSet['item_set']);
                    $options[$itemSetCustom] = $itemSet['item_set'];
                }
                return $options;
            }
        } catch (LocalizedException $e) {
        }
        return [];
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $result = [[
            'value' => '',
            'label' => 'Select Option'
        ]];
        foreach ($this->getOptionArray() as $id => $option) {
            $result[] = [
                'value' => $id,
                'label' => $option
            ];
        }
        return $result;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->getOptionArray();
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value)
    {

        $options = self::getOptionArray();

        return isset($options[$value]) ? $options[$value] : null;
    }
}