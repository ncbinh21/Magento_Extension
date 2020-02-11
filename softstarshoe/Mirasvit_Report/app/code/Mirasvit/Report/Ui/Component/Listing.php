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

use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Listing as UiListing;

class Listing extends UiListing
{
    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @param ContextInterface $context
     * @param FormatInterface  $localeFormat
     * @param array            $components
     * @param array            $data
     */
    public function __construct(
        ContextInterface $context,
        FormatInterface $localeFormat,
        array $components = [],
        array $data = []
    ) {
        $this->localeFormat = $localeFormat;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsConfig(UiComponentInterface $component)
    {
        $jsConfig = parent::getJsConfig($component);

        $jsConfig['report_type'] = 'abc' . rand(0, 10000);
        $jsConfig['priceFormat'] = $this->localeFormat->getPriceFormat();

        return $jsConfig;
    }
}
