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


namespace Mirasvit\Report\Ui\Component\Listing;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mirasvit\Report\Ui\Context;

class Paging extends \Magento\Ui\Component\Paging
{
    /**
     * @var Context
     */
    protected $uiContext;

    /**
     * @param ContextInterface $context
     * @param Context          $uiContext
     * @param array            $components
     * @param array            $data
     */
    public function __construct(
        ContextInterface $context,
        Context $uiContext,
        array $components = [],
        array $data = []
    ) {
        $this->uiContext = $uiContext;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();

        if (!$this->uiContext->getReport()->getGridConfig('paging')
            || $this->getContext()->getRequestParam('selected')
        ) {
            $this->getContext()->getDataProvider()->setLimit(0, 10000);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareOptions()
    {
        if (!$this->uiContext->getReport()->getGridConfig('paging')) {
            $config['template'] = 'report/empty';
            $this->setData('config', $config);
        } else {
            parent::prepareOptions();
        }
    }
}
