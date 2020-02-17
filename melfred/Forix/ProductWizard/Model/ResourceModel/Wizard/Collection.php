<?php


namespace Forix\ProductWizard\Model\ResourceModel\Wizard;
use \Forix\ProductWizard\Api\Data\WizardInterface;
class Collection extends \Forix\ProductWizard\Model\ResourceModel\AbstractCollection
{

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'wizard_collection';
    
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Forix\ProductWizard\Model\Wizard',
            'Forix\ProductWizard\Model\ResourceModel\Wizard'
        );
        $this->_map['fields']['wizard_id'] = 'main_table.wizard_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }
    
    
    

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|wizard_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = [];
        $existingIdentifiers = [];
        foreach ($this as $item) {
            $identifier = $item->getData('identifier');

            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('wizard_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);
        $this->performAfterLoad('forix_wizard_store', $entityMetadata->getLinkField());
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(WizardInterface::class);
        $this->joinStoreRelationTable('forix_wizard_store', $entityMetadata->getLinkField());
    }
}
