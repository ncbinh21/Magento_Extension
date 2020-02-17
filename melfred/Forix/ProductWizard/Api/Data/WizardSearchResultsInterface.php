<?php


namespace Forix\ProductWizard\Api\Data;

interface WizardSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Wizard list.
     * @return \Forix\ProductWizard\Api\Data\WizardInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param \Forix\ProductWizard\Api\Data\WizardInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
