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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mirasvit\Report\Ui\Component\Listing\Column;

use Magento\Customer\Api\Data\AttributeMetadataInterface as AttributeMetadata;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class AttributeColumn extends Column
{
    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @param ContextInterface    $context
     * @param UiComponentFactory  $uiComponentFactory
     * @param AttributeRepository $attributeRepository
     * @param array               $components
     * @param array               $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AttributeRepository $attributeRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return null;
        }

        $metaData = $this->attributeRepository->getMetadataByCode($this->getName());
        if ($metaData && count($metaData[AttributeMetadata::OPTIONS])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!isset($item[$this->getName()])) {
                    continue;
                }
                foreach ($metaData[AttributeMetadata::OPTIONS] as $option) {
                    if ($option['value'] == $item[$this->getName()]) {
                        $item[$this->getName()] = $option['label'];
                        break;
                    }
                }
            }
        }

        return $dataSource;
    }
}
