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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Seo\Ui\Template\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\Seo\Model\Config as Config;

class TableActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $config = $this->getConfiguration();
        if (isset($config['indexField']) && isset($config['pathEdit']) && isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$config['indexField']])) {
                    $type = $item['rule_type'];
                    switch ($type) {
                        case Config::PRODUCTS_RULE:
                            $path = 'editProduct';
                            break;

                        case Config::CATEGORIES_RULE:
                            $path = 'editCategory';
                            break;

                        case Config::RESULTS_LAYERED_NAVIGATION_RULE:
                            $path = 'editLayeredNavigation';
                            break;

                        default:
                            break;
                    }
                    $item[$this->getData('name')] = [
                        'view' => [
                            'href' => $this->urlBuilder->getUrl(
                                'seo/template/' . $path,
                                [
                                    'id' => $item[$config['indexField']],
                                ]
                            ),
                            'label' => isset($config['actionName']) ? $config['actionName'] : __('Edit'),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
