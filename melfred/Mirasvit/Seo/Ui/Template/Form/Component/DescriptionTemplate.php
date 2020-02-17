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


namespace Mirasvit\Seo\Ui\Template\Form\Component;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mirasvit\Seo\Model\ResourceModel\Template\Collection as TemplateCollection;
use Magento\Framework\App\RequestInterface;

class DescriptionTemplate extends Field
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param TemplateCollection $templateCollection
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        RequestInterface $request,
        TemplateCollection $templateCollection,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->templateCollection = $templateCollection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        if ($key == 'config'
            && isset($value['dataScope'])
            && $value['dataScope'] == 'description_template'
            && $this->isHidden()) {
            $value['visible'] = false;
        }

        parent::setData($key, $value);
    }

    /**
     * Check if field is hidden
     *
     * @return bool
     */
    protected function isHidden()
    {
        if ($id = $this->request->getParam('id')) {
            $item = $this->templateCollection
                ->addStoreColumn()
                ->addFieldToFilter('template_id', $id)
                ->getFirstItem();

            $descriptionPosition = $item->getData('description_position');
            if ($descriptionPosition != 5) {
                return true;
            }
        }

        return false;
    }
}