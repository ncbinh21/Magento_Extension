<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/06/2018
 * Time: 16:31
 */

namespace Forix\ProductWizard\Model\Source;


use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

class Group  implements SourceInterface, OptionSourceInterface
{

    protected $_optionHash = null;
    protected $_groupFactory;
    protected $_wizardCollectionFactory;
    public function __construct(
        \Forix\ProductWizard\Model\ResourceModel\Wizard\CollectionFactory $collectionFactory
    )
    {
        $this->_wizardCollectionFactory = $collectionFactory;
    }



    /**
     * Return array of options as value-label pairs
     *
     * @return array 
     */
    protected function getAllOptionArray()
    {
        if(null === $this->_optionHash) {
            /**
             * @var $collection \Forix\ProductWizard\Model\ResourceModel\Group\Collection
             * @var $wizard \Forix\ProductWizard\Model\Wizard
             */
            $wizardCollection = $this->_wizardCollectionFactory->create();
            foreach ($wizardCollection as $wizard) {
                $collection = $wizard->getGroups();
                $this->_optionHash[] = [
                    'wizard' => $wizard,
                    'group' => $collection->toOptionHash()
                ];
            }
        }
        return $this->_optionHash;
    }

    /**
     * Retrieve option array with empty value
     * Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];
        /**
         * @var $wizard \Forix\ProductWizard\Model\Wizard
         * @var $wizard \Forix\ProductWizard\Model\Wizard
         */
        foreach (self::getAllOptionArray() as $value) {
            if(isset($value['wizard'])) {
                $wizard = $value['wizard'];
                $groupItems = $value['group'];
                $values = [];
                foreach ($groupItems as $_index => $_value) {
                    $values[] = ['value' => $_index, 'label' => $_value];
                }
                $result[] = ['value' => $values, 'index' => $wizard->getId(), 'label' => $wizard->getTitle()];
            }
        }
        return $result;
    }

    /**
     *  Retrieve Option value text
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getAllOptionArray();
        foreach($options as $value) {
            $groupItems = $value['group_item'];
            if(isset($groupItems[$optionId])){
                return $groupItems[$optionId];
            }
        }
        return null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}