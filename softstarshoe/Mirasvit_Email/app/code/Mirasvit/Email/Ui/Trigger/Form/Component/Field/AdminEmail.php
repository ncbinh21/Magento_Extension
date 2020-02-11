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



namespace Mirasvit\Email\Ui\Trigger\Form\Component\Field;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Controller\Adminhtml\Trigger;

class AdminEmail extends Field
{
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * AdminEmail constructor.
     *
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param ContextInterface                     $context
     * @param UiComponentFactory                   $uiComponentFactory
     * @param array                                $components
     * @param array                                $data
     */
    public function __construct(
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        $components = [],
        array $data = []
    ) {
        $this->filterBuilder = $filterBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Show "admin_email" field if current trigger is of type Administrator.
     *
     * {@inheritDoc}
     */
    public function prepare()
    {
        $isAdmin = false;
        $dataProvider = $this->context->getDataProvider();
        $this->filterBuilder
            ->setField(TriggerInterface::ID)
            ->setValue($this->context->getRequestParam(TriggerInterface::ID));
        $dataProvider->addFilter($this->filterBuilder->create());

        /** @var TriggerInterface $item */
        foreach ($dataProvider->getSearchResult()->getItems() as $item) {
            $isAdmin = $item->getIsAdmin();
        }

        if ($this->getContext()->getRequestParam(TriggerInterface::IS_ADMIN, false) || $isAdmin) {
            $this->setData('config', array_merge($this->getData('config'), ['visible' => true]));
        }

        parent::prepare();
    }
}
