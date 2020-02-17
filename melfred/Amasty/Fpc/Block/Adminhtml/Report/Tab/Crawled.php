<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Report\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;

class Crawled extends Report implements TabInterface
{
    protected $_template = 'report/crawled.phtml';

    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log
     */
    private $logResource;

    public function __construct(
        Template\Context $context,
        \Amasty\Fpc\Model\ResourceModel\Log $logResource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logResource = $logResource;
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Warmed Pages');
    }

    protected function _getGraphData()
    {
        $stats = $this->logResource->getStatsByDay();

        foreach ($stats as &$row) {
            $row['period'] = $this->formatDate(
                $row['period'],
                \IntlDateFormatter::MEDIUM
            );
        }

        return $stats;
    }
}
