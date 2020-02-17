<?php

namespace Forix\ProductWizard\Plugin\Model;


use Forix\ProductWizard\Model\GroupItemFactory;
use Forix\ProductWizard\Model\ResourceModel\GroupItem\CollectionFactory;
use \Forix\ProductWizard\Helper\Data as HelperData;
class ConfigPlugin
{
    protected $collectionGroupItem;
    protected $groupItemFactory;
    protected $wizardFactory;

    public function __construct(
        \Forix\ProductWizard\Model\ResourceModel\GroupItem\CollectionFactory $collectionGroupItem,
        \Forix\ProductWizard\Model\GroupItemFactory $groupItemFactory,
        \Forix\ProductWizard\Model\WizardFactory $wizardFactory
    ) {
        $this->collectionGroupItem = $collectionGroupItem;
        $this->groupItemFactory = $groupItemFactory;
        $this->wizardFactory = $wizardFactory;
    }

    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    ) {
        if($subject->getSection() == 'wizard') {
            if ($subject->getGroups() && is_array($subject->getGroups())) {
                $groups = $subject->getGroups();
                foreach ($groups as $key => &$group) {
                    if ($group && isset($group['fields']) && isset($group['fields']['required_item_set']) && isset($group['fields']['required_item_set']['value'])) {
                        foreach ($group['fields']['required_item_set']['value'] as &$item) {
                            if (isset($item['item_set'])) {
                                $itemSetCustom = HelperData::cleanCssIdentifier($item['item_set']);
                                $item['identifier'] = $itemSetCustom;
                            }
                        }
                    }
                }
                $subject->setGroups($groups);
            }
        }
        $result = $proceed();
        if($subject->getSection() == 'wizard') {
            if ($subject->getGroups() && is_array($subject->getGroups())) {
                foreach ($subject->getGroups() as $key => $group) {
                    if ($group && isset($group['fields']) && isset($group['fields']['required_item_set']) && isset($group['fields']['required_item_set']['value'])) {
                        $key = str_replace('-', '_', $key);
                        $itemSet[$key] = [];
                        foreach ($group['fields']['required_item_set']['value'] as $item) {
                            if (isset($item['item_set'])) {
                                $itemSetCustom = HelperData::cleanCssIdentifier($item['item_set']);
                                array_push($itemSet[$key], $itemSetCustom);
                            }
                        }
                    }
                }
            }
            $groupItems = $this->collectionGroupItem->create();
            if ($groupItems->getSize() > 0) {
                foreach ($groupItems as $groupItem) {
                    $wizard = $this->wizardFactory->create()->load($groupItem->getWizardId());
                    $wizardInden = str_replace('-', '_', $wizard->getIdentifier());
                    if (isset($itemSet[$wizardInden]) && $groupItem->getItemSetId() && !in_array($groupItem->getItemSetId(), $itemSet[$wizardInden])) {
                        $item = $this->groupItemFactory->create()->load($groupItem->getId());
                        $item->delete();
                    }
                }
            }
        }

//        $groupItemOptions = $this->collectionGroupItemOption->create();
//        if($groupItemOptions->getSize() > 0) {
//            foreach ($groupItemOptions as $groupItemOption) {
//                $wizard = $this->wizardFactory->create()->load($groupItem->getWizardId());
//                $wizardInden = str_replace('-', '_', $wizard->getIdentifier());
//                if(isset($itemSet[$wizardInden]) && $groupItemOption->getItemSetId() && in_array($groupItemOption->getItemSetId(), $itemSet[$wizardInden])) {
//                    $item = $this->groupItemOptionFactory->create()->load($groupItemOption->getId());
//                    $item->delete();
//                }
//            }
//        }

        return $result;
    }
}
