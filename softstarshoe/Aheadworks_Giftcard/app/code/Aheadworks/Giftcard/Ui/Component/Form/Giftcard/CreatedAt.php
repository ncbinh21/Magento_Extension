<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\Component\Form\Giftcard;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;

/**
 * Class CreatedAt
 *
 * @package Aheadworks\Giftcard\Ui\Component\Form\Giftcard
 */
class CreatedAt extends \Aheadworks\Giftcard\Ui\Component\Form\Field
{
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $localeDate
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $localeDate,
        GiftcardRepositoryInterface $giftcardRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $giftcardRepository, $components, $data);
        $this->localeDate = $localeDate;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);
        if ($this->getGiftCardId() && isset($dataSource['data']['created_at']) && $dataSource['data']['created_at']) {
            $date = $dataSource['data']['created_at'];
            try {
                $createdAt = $this->localeDate->date($date, null, true)->format('d M Y H:i:s A');
            } catch (\Exception $e) {
                $createdAt = null;
            }
            $dataSource['data']['created_at'] = $createdAt;
        }
        return $dataSource;
    }
}
