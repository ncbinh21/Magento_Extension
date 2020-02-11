<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Subject
 * @package Aheadworks\Helpdesk\Ui\Component\Listing\Columns
 */
class FirstMessageContent extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
    /**
     * Filter
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filter;
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter,
        array $components = [],
        array $data = []
    ) {
        $this->_escaper = $escaper;
        $this->_filter = $filter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['first_message_content'] = strip_tags($item['first_message_content']);
            $item['first_message_content'] = str_replace(array("\t","\r","\n"),"",$item['first_message_content']);
            $item['first_message_content'] = $this->_escaper->escapeHtml($item['first_message_content']);
            $item['first_message_content'] = $this->_filter->truncate($item['first_message_content'],['length' => 250]);
        }

        return $dataSource;
    }
}
