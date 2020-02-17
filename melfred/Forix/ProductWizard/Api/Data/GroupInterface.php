<?php


namespace Forix\ProductWizard\Api\Data;

interface GroupInterface
{

    const ID = 'id';
    const GROUP_ID = 'group_id';
    const TITLE = 'title';
    const STEP_ID = 'step_id';


    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     */
    public function setGroupId($groupId);


    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     */
    public function setTitle($title);

    /**
     * Get step_id
     * @return string|null
     */
    public function getStepId();

    /**
     * Set step_id
     * @param string $stepId
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     */
    public function setStepId($stepId);
}
