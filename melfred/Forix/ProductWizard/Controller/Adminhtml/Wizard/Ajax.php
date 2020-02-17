<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 */
namespace Forix\ProductWizard\Controller\Adminhtml\Wizard;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use \Forix\ProductWizard\Helper\Data as HelperData;

class Ajax extends Action
{
    /**
     * @var \Forix\ProductWizard\Model\GroupRepository
     */
    protected $groupRepository;

    /**
     * @var \Forix\ProductWizard\Model\WizardRepository
     */
    protected $wizardRepository;

    /**
     * Ajax constructor.
     * @param \Forix\ProductWizard\Model\GroupRepository $groupItemRepository
     * @param \Forix\ProductWizard\Model\WizardRepository $wizardRepository
     * @param Context $context
     */
    public function __construct(
        \Forix\ProductWizard\Model\GroupRepository $groupRepository,
        \Forix\ProductWizard\Model\WizardRepository $wizardRepository,
        Context $context
    ) {
        $this->groupRepository = $groupRepository;
        $this->wizardRepository = $wizardRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('group_id', false)) {
            return [];
        }
        $groupId = $this->getRequest()->getParam('group_id');
        try {
            $group = $this->groupRepository->getById($groupId);
            $wizard = $this->wizardRepository->getById($group->getWizardId());//
            $itemSets = $wizard->getRequiredItemSets();
            $options = [];
            foreach ($itemSets as $itemSet) {
                $itemSetCustom = HelperData::cleanCssIdentifier($itemSet['item_set']);
                $options[$itemSetCustom] = $itemSet['item_set'];
            }

            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($options);
            return $resultJson;

        } catch (LocalizedException $e) {
            return [];
        }
        return [];
    }
}
