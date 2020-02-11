<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Form;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Fieldset
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form
 */
class Fieldset extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param ContextInterface $context
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        GiftcardRepositoryInterface $giftcardRepository,
        OrderRepositoryInterface $orderRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->giftcardRepository = $giftcardRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $orderId = $this->getGiftCardOrderId();
        $giftcard = $this->getGiftcard();
        $giftcardId = !$giftcard ? : $giftcard->getId();

        if ((isset($config['visibleIsSetGcId']) && !$config['visibleIsSetGcId'] && $giftcardId) ||
            (isset($config['visibleIsSetGcId']) && $config['visibleIsSetGcId'] && !$giftcardId) ||
            (isset($config['visibleIsSetOrderId']) && !$config['visibleIsSetOrderId'] && $orderId) ||
            (isset($config['visibleIsSetOrderId']) && $config['visibleIsSetOrderId'] && !$orderId)
        ) {
            $config['componentDisabled'] = true;
        }
        $this->setData('config', $config);
    }

    /**
     * Retrieve current gift card id
     *
     * @return GiftcardInterface|null
     */
    public function getGiftcard()
    {
        $giftcardId = $this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName(),
            null
        );
        try {
            return $this->giftcardRepository->get($giftcardId);
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Retrieve current gift card order id
     *
     * @return string|null
     */
    private function getGiftCardOrderId()
    {
        $giftcard = $this->getGiftcard();
        if ($giftcard && $giftcard->getOrderId()) {
            try {
                return $this->orderRepository->get($giftcard->getOrderId())->getEntityId();
            } catch (NoSuchEntityException $e) {
            }
        }
        return null;
    }
}
