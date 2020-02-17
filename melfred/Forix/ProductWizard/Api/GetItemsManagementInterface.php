<?php


namespace Forix\ProductWizard\Api;

interface GetItemsManagementInterface
{


    /**
     * GET for getItems api
     * @param string $param
     * @return \Forix\ProductWizard\Api\Data\WizardInterface
     */
    public function getGetItems($param);
}
