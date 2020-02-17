<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Block\Adminhtml\Report\Tab;

use Amasty\Fpc\Helper\Http as HttpHelper;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\View\Element\Template;

class Status extends Report implements TabInterface
{
    protected $_template = 'report/status.phtml';

    /**
     * @var \Amasty\Fpc\Model\ResourceModel\Log
     */
    private $logResource;
    /**
     * @var HttpHelper
     */
    private $httpHelper;

    public function __construct(
        Template\Context $context,
        \Amasty\Fpc\Model\ResourceModel\Log $logResource,
        HttpHelper $httpHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logResource = $logResource;
        $this->httpHelper = $httpHelper;
    }

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __('Status Codes');
    }

    protected function _getGraphData()
    {
        $result = [];
        $stats = $this->logResource->getStatsByStatus();

        $statusColors = [
            HttpHelper::STATUS_ALREADY_CACHED => 'gray',
            200 => 'green',
            500 => 'red',
            404 => 'orange',
        ];

        foreach ($stats as $code => $count) {
            $status = $this->httpHelper->getStatusCodeDescription($code);

            if ($code != HttpHelper::STATUS_ALREADY_CACHED) {
                $status = $code . ' ' . $status;
            }

            $row = [
                'status' => $status,
                'count'  => $count,
                'code'   => $code
            ];

            if ($code == 200) {
                $row['suffix'] = ' Warmed Pages';
            }

            if (isset($statusColors[$code])) {
                $row['color'] = $statusColors[$code];
            } // else assign random color

            $result [] = $row;
        }

        return $result;
    }
}
