<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Repository\Email;

use Mirasvit\Report\Api\Repository\Email\BlockRepositoryInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponentFactoryFactory;
use Magento\Framework\Registry;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\Component\Listing\Columns;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Ui\Api\BookmarkManagementInterface;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BlockRepository implements BlockRepositoryInterface
{
    /**
     * @var UiComponentInterface
     */
    static $component;

    /**
     * @var UiComponentFactoryFactory
     */
    protected $uiComponentFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ReportRepositoryInterface
     */
    protected $reportRepository;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * @var DateServiceInterface
     */
    protected $dateService;

    /**
     * @var AbstractReport
     */
    protected $report;

    /**
     * @var BookmarkInterface
     */
    protected $bookmark;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        UiComponentFactoryFactory $uiComponentFactory,
        Registry $registry,
        MetadataProvider $metadataProvider,
        RequestInterface $request,
        \Mirasvit\Report\Model\Config\Map $map,
        BookmarkManagementInterface $bookmarkManagement,
        DateServiceInterface $dateService,
        PricingHelper $pricingHelper
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        $this->registry = $registry;
        $this->reportRepository = $reportRepository;
        $this->metadataProvider = $metadataProvider;
        $this->request = $request;
        $this->map = $map;
        $this->bookmarkManagement = $bookmarkManagement;
        $this->dateService = $dateService;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        $blocks = [];
        foreach ($this->reportRepository->getList() as $report) {
            $blocks[$report->getIdentifier()] = $report->getName() . '';
        }

        return $blocks;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getContent($identifier, $data)
    {
        return $this->build($data);
    }

    /**
     * @param array $reportData
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function build(array $reportData)
    {
        $this->emulateConfiguration($reportData);

        if (self::$component == null) {
            self::$component = $this->uiComponentFactory->create()->create('mreport');
        }

        $component = self::$component;

        /** @var \Mirasvit\Report\Ui\DataProvider $dataProvider */
        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->reset();

        $this->prepareComponent(self::$component);

        $columns = $this->getColumns($component);

        $rows = [];
        foreach ($columns as $column) {
            $rows['header'][] = $column['label'];
        }

        $dataProvider->setLimit(0, $reportData['limit'] ? $reportData['limit'] : 1000000);

        $data = $dataProvider->getData();

        foreach ($data['items'] as $idx => $item) {
            foreach ($columns as $column) {
                $value = $item[$column['code']];

                $value = $this->prepareValue($value, $column);

                $rows[$idx][] = $value;
            }
        }

        foreach ($data['totals'] as $idx => $item) {
            foreach ($columns as $column) {
                $value = $item[$column['code']];

                $value = $this->prepareValue($value, $column);

                $rows['footer'][] = $value;
            }
        }

        $table = '<table>';
        foreach ($rows as $idx => $row) {
            $table .= '<tr>';
            foreach ($row as $column) {
                if ($idx === 'header' || $idx === 'footer') {
                    $table .= '<th>' . $column . '</th>';
                } else {
                    $table .= '<td>' . $column . '</td>';
                }
            }
            $table .= '</tr>';
        }

        $table .= '</table>';

        $name = $this->report->getName();
        if ($this->bookmark) {
            $name .= " <small>({$this->bookmark->getTitle()})</small>";
        }

        return "
            <h2>{$name}</h2>
            <div class='interval'>{$this->dateService->getIntervalHint($reportData['time_range'])}</div>
            
            <div class='table-wrapper'>$table</div>
        ";
    }

    /**
     * @param array $reportData
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function emulateConfiguration($reportData)
    {
        list($reportIdentifier, $viewIdentifier) = explode('/', $reportData['identifier'] . '/');

        $this->request->setParams([
            'namespace' => 'report',
        ]);

        $this->report = $this->reportRepository->get($reportIdentifier);

        $this->registry->unregister('current_report');
        $this->registry->register('current_report', $this->report);

        $_GET['columns'] = [];

        if (!$viewIdentifier) {
            $_GET['columns'] = [
                $this->report->getDefaultDimension()->getName(),
            ];
            foreach ($this->report->getDefaultColumns() as $column) {
                $_GET['columns'][] = $column->getName();
            }
            $_GET['dimension'] = $this->report->getDefaultDimension()->getName();
        } else {
            $bookmark = $this->bookmarkManagement->getByIdentifierNamespace($viewIdentifier, $reportIdentifier);
            if ($bookmark) {
                $this->bookmark = $bookmark;

                foreach ($bookmark->getConfig()['views'] as $view) {
                    if ($view['index'] == $viewIdentifier) {
                        foreach ($view['data']['columns'] as $column => $data) {
                            if ($data['visible']) {
                                $_GET['columns'][] = $column;
                            }
                        }
                    }
                }
            }
        }

        $filters = [];
        foreach ($this->report->getFastFilters() as $filter) {
            if (strpos($filter->getName(), 'created_at')) {
                $interval = $this->dateService->getInterval($reportData['time_range']);
                $filters[$filter->getName()]['from'] = $interval['from'];
                $filters[$filter->getName()]['to'] = $interval['to'];
            }
        }

        $this->request->setParams([
            'namespace' => 'report',
            'filters'   => $filters,
        ]);
    }

    /**
     * @param UiComponentInterface $component
     * @return void
     */
    protected function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }
        if ($component instanceof Column) {

        } else {
            $component->prepare();
        }
    }

    /**
     * @param UiComponentInterface $component
     * @return array
     */
    protected function getColumns(UiComponentInterface $component)
    {
        $columns = [];
        /** @var \Magento\Ui\Component\Listing\Columns\Column $column */
        foreach ($this->getColumnsComponent($component)->getChildComponents() as $key => $column) {
            if ($column->getData('config/add_field')) {
                $order = $column->getData('config/sortOrder');

                if ($column->getData('config/dimension')) {
                    $order = 100000;
                }
                $columns[$order] = [
                    'code'      => $key,
                    'label'     => $column->getData('config/label'),
                    'sortOrder' => $order,
                    'type'      => $column->getData('config/type'),
                    'options'   => $column->getData('config/options'),
                ];
            }
        }
        krsort($columns);

        return $columns;
    }

    /**
     * @param UiComponentInterface $component
     * @return UiComponentInterface
     * @throws \Exception
     */
    protected function getColumnsComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $childComponent) {
            if ($childComponent instanceof Columns) {
                return $childComponent;
            }
        }

        throw new \Exception('No columns component found');
    }

    /**
     * @param string $value
     * @param array  $column
     * @return string
     */
    protected function prepareValue($value, $column)
    {
        if ($column['type'] == 'number') {
            $value = round($value, 2);
        } elseif ($column['type'] == 'price') {
            $value = $this->pricingHelper->currency($value);
        } elseif ($column['type'] == 'select') {
            if (is_array($column['options'])) {
                foreach ($column['options'] as $option) {
                    if ($option['value'] == $value) {
                        $value = $option['label'];
                    }
                }
            }
        }

        return $value;
    }
}
