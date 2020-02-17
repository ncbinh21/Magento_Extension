<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 14:14
 */

namespace Forix\CatalogImport\Model;
class Import extends \Magento\ImportExport\Model\Import
{

    public function getBasedEntity()
    {
        if (php_sapi_name() == 'cli') {
            return $this->getData('based_entity');
        }
        if('melfred_catalog_product' == $this->getData('entity')){
            return 'catalog_product';
        }
        return $this->getData('entity');
    }

    /**
     * Create instance of entity adapter and return it
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\ImportExport\Model\Import\Entity\AbstractEntity|\Magento\ImportExport\Model\Import\AbstractEntity
     */
    protected function _getEntityAdapter()
    {
        if (!$this->_entityAdapter) {
            $entities = $this->_importConfig->getEntities();
            if (isset($entities[$this->getBasedEntity()])) {
                try {
                    $this->_entityAdapter = $this->_entityFactory->create($entities[$this->getBasedEntity()]['model']);
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Please enter a correct entity model.')
                    );
                }
                if (!$this->_entityAdapter instanceof \Magento\ImportExport\Model\Import\Entity\AbstractEntity &&
                    !$this->_entityAdapter instanceof \Magento\ImportExport\Model\Import\AbstractEntity
                ) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __(
                            'The entity adapter object must be an instance of %1 or %2.', \Magento\ImportExport\Model\Import\Entity\AbstractEntity::class, \Magento\ImportExport\Model\Import\AbstractEntity::class
                        )
                    );
                }

                // check for entity codes integrity
                if ($this->getEntity() != $this->_entityAdapter->getEntityTypeCode()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The input entity code is not equal to entity adapter code.')
                    );
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a correct entity.'));
            }
            $this->_entityAdapter->setParameters($this->getData());
        }
        return $this->_entityAdapter;
    }
}