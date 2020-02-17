<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/10/17
 * Time: 4:30 PM
 */

namespace Forix\ImportHelper\Model\Export;

class RawData
{


    protected $_logger;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\Csv
     */
    protected $_writer;
    /**
     * Items per page for collection limitation
     *
     * @var null
     */
    protected $_itemsPerPage = null;

    /**
     * @var ExportRawInterface
     */
    protected $_entityModel;


    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
    }

    /**
     * @param $model ExportRawInterface
     */
    public function setEntityModel(ExportRawInterface $model)
    {
        $this->_entityModel = $model;
    }


    /**
     * @return \Magento\ImportExport\Model\Export\Adapter\Csv
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getWriter()
    {
        if (!$this->_writer) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please specify the writer.'));
        }
        return $this->_writer;
    }

    public function setWriter($writer)
    {
        $this->_writer = $writer;
        return $this;
    }

    /**
     * Get items per page
     *
     * @return int
     */
    protected function getItemsPerPage()
    {
        if ($this->_itemsPerPage === null) {
            $memoryLimit = trim(ini_get('memory_limit'));
            $lastMemoryLimitLetter = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
            switch ($lastMemoryLimitLetter) {
                case 'g':
                    $memoryLimit *= 1024;
                // fall-through intentional
                case 'm':
                    $memoryLimit *= 1024;
                // fall-through intentional
                case 'k':
                    $memoryLimit *= 1024;
                    break;
                default:
                    // minimum memory required by Magento
                    $memoryLimit = 250000000;
            }

            // Tested one product to have up to such size
            $memoryPerProduct = 100000;
            // Decrease memory limit to have supply
            $memoryUsagePercent = 0.8;
            // Minimum Products limit
            $minProductsLimit = 500;
            // Maximal Products limit
            $maxProductsLimit = 5000;

            $this->_itemsPerPage = intval(
                ($memoryLimit * $memoryUsagePercent - memory_get_usage(true)) / $memoryPerProduct
            );
            if ($this->_itemsPerPage < $minProductsLimit) {
                $this->_itemsPerPage = $minProductsLimit;
            }
            if ($this->_itemsPerPage > $maxProductsLimit) {
                $this->_itemsPerPage = $maxProductsLimit;
            }
        }
        return $this->_itemsPerPage;
    }

    protected function _getEntityCollection($resetCollection = false)
    {
        return $this->_entityModel->getCollection($resetCollection);
    }

    public function exports()
    {
        //Execution time may be very long
        set_time_limit(0);

        $writer = $this->getWriter();
        $page = 0;
        while (true) {
            ++$page;
            $entityCollection = $this->_getEntityCollection(true);
            $entityCollection->setOrder('rawdata_id', 'asc');
            $entityCollection->setCurPage($page)->setPageSize($this->getItemsPerPage());

            if ($entityCollection->count() == 0) {
                break;
            }
            $exportData = $this->getExportData();
            if ($page == 1) {
                $writer->setHeaderCols($this->getColumns());
            }
            foreach ($exportData as $dataRow) {
                $writer->writeRow($dataRow);
            }
            if ($entityCollection->getCurPage() >= $entityCollection->getLastPageNumber()) {
                break;
            }
        }
        return $writer->getContents();
    }


    protected function getExportData()
    {
        $exportData = [];
        try {
            $exportData = $this->collectRawData();
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }
        return $exportData;
    }

    /**
     * Collect export data for all products
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function collectRawData()
    {
        $data = [];
        $collection = $this->_getEntityCollection();
        foreach ($collection as $itemId => $item) {
            $_data = $item->getData();
            if(isset($_data['error_list'])){
                $_data['error_list'] = '';
            }
            $data[] = $_data;
        }
        $collection->clear();
        return $data;
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        return array_keys($this->_entityModel->getCollection()->getFirstItem()->getData());
    }

}