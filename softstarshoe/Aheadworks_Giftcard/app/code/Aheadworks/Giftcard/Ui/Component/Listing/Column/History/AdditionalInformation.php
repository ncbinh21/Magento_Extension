<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Listing\Column\History;

use Aheadworks\Giftcard\Model\Giftcard\History\CommentInterface;
use Aheadworks\Giftcard\Model\Giftcard\History\CommentPool;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\Giftcard\Model\Source\History\Action as HistoryAction;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class AdditionalInformation
 *
 * @package Aheadworks\Giftcard\Ui\Component\Listing\Column\History
 */
class AdditionalInformation extends Column
{
    /**
     * @var CommentPool
     */
    private $commentPool;

    /**
     * @var HistoryAction
     */
    private $historyActions;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param HistoryAction $historyActions
     * @param CommentPool $commentPool
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        HistoryAction $historyActions,
        CommentPool $commentPool,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->historyActions = $historyActions;
        $this->commentPool = $commentPool;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['action_type'])) {
                    /** @var CommentInterface $commentInstance */
                    if ($commentInstance = $this->commentPool->get($item['action_type'])) {
                        $commentLabel = $commentInstance->renderComment(
                            $item['entities'],
                            $item['comment_placeholder'],
                            true
                        );
                    }

                    if (!empty($commentLabel)) {
                        $item['comment'] = $commentLabel;
                    }
                }
            }
        }

        return $dataSource;
    }
}
