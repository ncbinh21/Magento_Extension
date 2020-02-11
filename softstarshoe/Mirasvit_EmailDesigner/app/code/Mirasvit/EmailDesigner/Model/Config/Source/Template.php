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
 * @package   mirasvit/module-email-designer
 * @version   1.0.16
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;
use Mirasvit\EmailDesigner\Api\Repository\TemplateRepositoryInterface;

class Template implements ArrayInterface
{
    /**
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * Priority constructor.
     *
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(TemplateRepositoryInterface $templateRepository)
    {
        $this->templateRepository= $templateRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return $this->templateRepository->getCollection()->toOptionArray();
    }
}
