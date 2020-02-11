<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Giftcard\History;

use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;

/**
 * Class Comment
 *
 * @package Aheadworks\Giftcard\Model\Giftcard\History
 */
class Comment implements CommentInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $label;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @param UrlInterface $urlBuilder
     * @param Placeholder $placeholder
     * @param int|null $type
     * @param string|null $label
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Placeholder $placeholder,
        $type = null,
        $label = null
    ) {
        $this->type = $type;
        $this->label = $label;
        $this->urlBuilder = $urlBuilder;
        $this->placeholder = $placeholder;
    }

    /**
     *  {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *  {@inheritDoc}
     */
    public function getLabel($arguments = [])
    {
        return __($this->label, $arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function renderComment(
        $arguments,
        $label = null,
        $renderingUrl = false
    ) {
        $labelArguments = [];
        if ($arguments) {
            /** @var HistoryEntityInterface $entity */
            foreach ($arguments as $entity) {
                switch ($entity->getEntityType()) {
                    case SourceHistoryEntityType::ORDER_ID:
                        $labelArguments['order_id'] = '#' . $entity->getEntityLabel();
                        if ($renderingUrl) {
                            $labelArguments['order_url'] = $this->getOrderUrl($entity->getEntityId());
                            $label = str_replace(
                                '%order_id',
                                '<a href="%order_url">%order_id</a>',
                                $label
                            );
                        }
                        break;
                    case SourceHistoryEntityType::CREDIT_MEMO_ID:
                        $labelArguments['creditmemo_id'] = '#' . $entity->getEntityLabel();
                        if ($renderingUrl) {
                            $labelArguments['creditmemo_url'] = $this->getCreditMemoUrl($entity->getEntityId());
                            $label = str_replace(
                                '%creditmemo_id',
                                '<a href="%creditmemo_url">%creditmemo_id</a>',
                                $label
                            );
                        }
                        break;
                    case SourceHistoryEntityType::ADMIN_ID:
                        $labelArguments['name'] = $entity->getEntityLabel();
                        break;
                    case SourceHistoryEntityType::EMAIL_STATUS:
                        $labelArguments['status'] = $entity->getEntityLabel();
                        break;
                    case SourceHistoryEntityType::FROM:
                        $labelArguments['from'] = $entity->getEntityLabel();
                        break;
                    case SourceHistoryEntityType::TO:
                        $labelArguments['to'] = $entity->getEntityLabel();
                        break;
                }
            }
        }

        return $renderingUrl
            ? $this->placeholder->render([$label], $labelArguments)
            : $this->getLabel($labelArguments);
    }

    /**
     * Retrieve order url
     *
     * @param int $orderId
     * @return string
     */
    private function getOrderUrl($orderId)
    {
        return $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * Retrieve credit memo url
     *
     * @param int $creditMemoId
     * @return string
     */
    private function getCreditMemoUrl($creditMemoId)
    {
        return $this->urlBuilder->getUrl('sales/order_creditmemo/view', ['creditmemo_id' => $creditMemoId]);
    }
}
