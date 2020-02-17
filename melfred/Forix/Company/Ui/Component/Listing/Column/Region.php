<?php
/**
 * Created by PhpStorm.
 * User: hai
 * Date: 28/06/2019
 * Time: 12:04
 */
namespace Forix\Company\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Forix\Company\Helper\Data;

class Region extends Column
{
    /**
     * @var Data
     */
    public $helper;

    /**
     * Region constructor.
     * @param Data $helper
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Data $helper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $regionLabel = $item['region'];
                if (is_null($regionLabel)) {
                    $regionLabel = $this->helper->getRegionDefaultName($item['region_id']);
                }
                $item[$this->getData('name')] = $regionLabel;
            }
        }

        return $dataSource;
    }
}