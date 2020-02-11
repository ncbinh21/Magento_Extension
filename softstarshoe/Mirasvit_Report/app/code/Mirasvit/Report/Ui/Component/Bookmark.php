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


namespace Mirasvit\Report\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Mirasvit\Report\Ui\Context;
use Magento\Framework\View\Element\UiComponentInterface;

class Bookmark extends \Magento\Ui\Component\Bookmark
{
    /**
     * @var Context
     */
    protected $uiContext;

    /**
     * @param Context                     $uiContext
     * @param ContextInterface            $context
     * @param BookmarkRepositoryInterface $bookmarkRepository
     * @param BookmarkManagementInterface $bookmarkManagement
     * @param array                       $components
     * @param array                       $data
     */
    public function __construct(
        Context $uiContext,
        ContextInterface $context,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        array $components,
        array $data
    ) {
        $this->uiContext = $uiContext;

        parent::__construct($context, $bookmarkRepository, $bookmarkManagement, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {

        $namespace = $this->uiContext->getReport()->getIdentifier();

        $config = [];

        if (!empty($namespace)) {
            $bookmarks = $this->bookmarkManagement->loadByNamespace($namespace);
            /** @var \Magento\Ui\Api\Data\BookmarkInterface $bookmark */
            foreach ($bookmarks->getItems() as $bookmark) {
                if ($bookmark->isCurrent()) {
                    $config['activeIndex'] = $bookmark->getIdentifier();
                }

                $config = array_merge_recursive($config, $bookmark->getConfig());
            }
        }

        /** add filters from GET to js config */
        $config['current'] = isset($config['current']) ? $config['current'] : ['filters' => ['applied' => []]];
        if (isset($config['current'])) {
            $config['current']['filters']['applied'] = array_merge(
                $config['current']['filters']['applied'],
                $this->context->getFiltersParams()
            );
            // filters convert applied to array not object without this line
            $config['current']['filters']['applied']['null'] = 'null';
        }

        $config['storageConfig']['namespace'] = $namespace;

        $this->setData('config', array_replace_recursive($this->getConfiguration($this), $config));

        $this->getContext()->addComponentDefinition($this->getComponentName(), $config);
    }
}
