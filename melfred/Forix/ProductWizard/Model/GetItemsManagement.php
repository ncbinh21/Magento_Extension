<?php


namespace Forix\ProductWizard\Model;

use \Magento\Framework\Data\Collection;

class GetItemsManagement implements \Forix\ProductWizard\Api\GetItemsManagementInterface
{

    protected $_wizardRepository;
    protected $_wizardFactory;
    public function __construct(
        \Forix\ProductWizard\Model\WizardFactory $wizardFactory,
        \Forix\ProductWizard\Model\WizardRepository $wizardRepository
    )
    {
        $this->_wizardFactory = $wizardFactory;
        $this->_wizardRepository = $wizardRepository;
    }

    /**
     * @param string $param
     * @return \Forix\ProductWizard\Api\Data\WizardInterface|Wizard|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGetItems($param)
    {
        if ($param) {
            $params = json_decode($param, true);
            $optionValue = $params['option_value'];
            $wizardId = $params['wizard_id'];
            $sql = <<<SQL
select wizard_id from(select a.group_item_option_id,
       a.depend_on,
       a.best_on,
       a.wizard_id,
       concat(b.item_id, '_', b.group_item_option_id) as `modify_id`
from forix_productwizard_group_item_option as a
    left join  forix_productwizard_wizard as e on (a.wizard_id = e.wizard_id)
    left join forix_productwizard_group_item as c on (c.group_item_id = a.item_id)
    left join forix_productwizard_group as d on (c.group_id = d.group_id)
    inner join (select * from forix_productwizard_group_item_option where option_value = {$optionValue}) as b
where a.item_id <> b.item_id and a.wizard_id != 0 and d.step_id > 1 
having (a.best_on is not null and find_in_set(modify_id, a.best_on))
    OR (a.depend_on is not null and not find_in_set(modify_id, a.depend_on) and find_in_set(modify_id, a.best_on))
    OR (a.best_on IS NULL AND a.depend_on IS NULL) 
order by e.sort_order asc) as rs group by rs.wizard_id;
SQL;
            $resource = $this->_wizardFactory->create()->getResource();
            $results = $resource->getConnection()->fetchCol($sql);
            foreach ($results as $result){
                if($wizardId == $result) {
                    return $this->_wizardRepository->getById($result);
                }
            }
            foreach ($results as $result){
                return $this->_wizardRepository->getById($result);
            }
        }
        return null;
    }
}