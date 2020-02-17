<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 31/10/2018
 * Time: 16:01
 */
namespace Forix\ProductWizard\Ui\Component\Listing\Columns;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use \Forix\ProductWizard\Helper\Data as HelperData;

class ItemSet extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $wizardRepository;
    public function __construct(
        ContextInterface $context,
        \Forix\ProductWizard\Model\WizardRepository $wizardRepository,
        UiComponentFactory $uiComponentFactory,
        array $components = [], array $data = [])
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->wizardRepository = $wizardRepository;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if($item['wizard_id']) {
                    $wizard = $this->wizardRepository->getById($item['wizard_id']);//$this->getData('name')
                    $itemSets = $wizard->getRequiredItemSets();
                    foreach ($itemSets as $itemSet) {
                        $itemSetCustom = HelperData::cleanCssIdentifier($itemSet['item_set']);
                        if ($itemSetCustom == $item[$this->getData('name')]) {
                            $item[$this->getData('name')] = $itemSet['item_set'];
                            break;
                        }
                    }
                }
            }
        }

        return $dataSource;
    }
}