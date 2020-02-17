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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Ui\CanonicalRewrite\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [
                    'edit'   => [
                        'href'  => $this->urlBuilder->getUrl('seo/canonicalRewrite/edit', [
                            CanonicalRewriteInterface::ID_ALIAS => $item[CanonicalRewriteInterface::ID],
                        ]),
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href'    => $this->urlBuilder->getUrl('seo/canonicalRewrite/delete', [
                            CanonicalRewriteInterface::ID_ALIAS => $item[CanonicalRewriteInterface::ID],
                        ]),
                        'label'   => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete item?'),
                        ],
                    ],
                ];
            }
        }

        return $dataSource;
    }
}