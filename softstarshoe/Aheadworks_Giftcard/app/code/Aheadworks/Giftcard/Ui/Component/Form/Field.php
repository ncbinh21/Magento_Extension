<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Form;

use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Field
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form
 */
class Field extends \Magento\Ui\Component\Form\Field
{
    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GiftcardRepositoryInterface $giftcardRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->giftcardRepository = $giftcardRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $giftcardId = $this->getGiftcardId();

        if ((isset($config['visibleIsSetGcId']) && !$config['visibleIsSetGcId'] && $giftcardId) ||
            (isset($config['visibleIsSetGcId']) && $config['visibleIsSetGcId'] && !$giftcardId)
        ) {
            $config['componentDisabled'] = true;
        }

        if ($configSettingsUrl = $this->getData('config/service/configSettingsUrl')) {
            $config['service']['configSettingsUrl'] = $this->getContext()->getUrl($configSettingsUrl);
        }
        $this->setData('config', $config);
    }

    /**
     * Retrieve current gift card id
     *
     * @return int|null
     */
    public function getGiftcardId()
    {
        $giftcardId = $this->getContext()->getRequestParam(
            $this->getContext()->getDataProvider()->getRequestFieldName(),
            null
        );
        try {
            return $this->giftcardRepository->get($giftcardId)->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }
}
