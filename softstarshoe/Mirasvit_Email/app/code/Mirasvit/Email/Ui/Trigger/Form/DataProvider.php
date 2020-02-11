<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Trigger\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Email\Api\Data\TriggerChainInterface as ChainInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Api\Repository\Trigger\ChainRepositoryInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class DataProvider extends AbstractDataProvider
{
    const TRIGGER = 'trigger';
    const CHAIN   = 'chain';

    /**
     * @var TriggerRepositoryInterface
     */
    private $triggerRepository;

    /**
     * @var UiComponentFactory
     */
    private $uiComponentFactory;

    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var ChainRepositoryInterface
     */
    private $chainRepository;

    public function __construct(
        ChainRepositoryInterface $chainRepository,
        TriggerRepositoryInterface $triggerRepository,
        UiComponentFactory $uiComponentFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->chainRepository = $chainRepository;
        $this->triggerRepository = $triggerRepository;
        $this->collection = $this->triggerRepository->getCollection();
        $this->uiComponentFactory = $uiComponentFactory;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var $item TriggerInterface */
        foreach ($this->collection as $item) {
            $item = $this->triggerRepository->get($item->getId());
            $result = $item->getData();
            $result[TriggerInterface::CANCELLATION_EVENT] = $item->getCancellationEvent(); // convert to array
            unset($result[TriggerInterface::RULE]); // remove dynamic fields
            /** @var $chain ChainInterface */
            foreach ($item->getChainCollection() as $chain) {
                $chain = $this->chainRepository->get($chain->getId());
                $result[self::CHAIN][$chain->getId()] = $chain->getData();
                $result[self::CHAIN][$chain->getId()][ChainInterface::EXCLUDE_DAYS] = $chain->getExcludeDays();
            }

            $this->loadedData[$item->getId()] = $result;
        }

        return $this->loadedData;
    }
}
