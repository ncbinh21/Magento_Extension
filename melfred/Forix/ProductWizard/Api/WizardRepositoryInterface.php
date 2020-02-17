<?php


namespace Forix\ProductWizard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface WizardRepositoryInterface
{


    /**
     * Save Wizard
     * @param \Forix\ProductWizard\Api\Data\WizardInterface $wizard
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\ProductWizard\Api\Data\WizardInterface $wizard
    );

    /**
     * Retrieve Wizard
     * @param string $wizardId
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($wizardId);

    /**
     * Retrieve Wizard matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\ProductWizard\Api\Data\WizardSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Wizard
     * @param \Forix\ProductWizard\Api\Data\WizardInterface $wizard
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\ProductWizard\Api\Data\WizardInterface $wizard
    );

    /**
     * Delete Wizard by ID
     * @param string $wizardId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($wizardId);
}
