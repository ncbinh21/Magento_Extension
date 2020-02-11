<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Form\Giftcard;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\Giftcard\Model\Config;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;

/**
 * Class ExpireAfter
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form\Giftcard
 */
class ExpireAfter extends \Aheadworks\Giftcard\Ui\Component\Form\Field
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Config $config
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Config $config,
        GiftcardRepositoryInterface $giftcardRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $giftcardRepository, $components, $data);
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['value'] = $this->config->getGiftcardExpireDays();
        $this->setData('config', $config);
    }
}
