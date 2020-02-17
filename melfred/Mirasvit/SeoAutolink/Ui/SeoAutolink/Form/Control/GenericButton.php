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



namespace Mirasvit\SeoAutolink\Ui\SeoAutolink\Form\Control;

use Magento\Backend\Block\Widget\Context;
use Mirasvit\SeoAutolink\Model\ResourceModel\Link\CollectionFactory;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        CollectionFactory $collectionFactory,
        Context $context
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->context = $context;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    /**
     * @return ProgramInterface|false
     */
    public function getModel()
    {
        return $this->collectionFactory->get($this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
