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

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\Report\Ui\Context;

class Chart extends AbstractComponent
{
    /**
     * @var Context
     */
    protected $uiContext;

    /**
     * @param Context          $uiContext
     * @param ContextInterface $context
     * @param array            $components
     * @param array            $data
     */
    public function __construct(
        Context $uiContext,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->uiContext = $uiContext;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'chart';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->prepareOptions();

        parent::prepare();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareOptions()
    {
        $config = $this->getData('config');

        if ($this->uiContext->getReport()->getChartConfig()->getData('chartType') == false) {
            $config['template'] = 'report/empty';

            $this->setData('config', $config);
            return;
        }

        $config = array_merge_recursive($config, $this->uiContext->getReport()->getChartConfig()->getData());

        $this->setData('config', $config);
    }
}
