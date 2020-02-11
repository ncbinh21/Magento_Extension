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


namespace Mirasvit\Report\Block\Adminhtml\Email\Edit\Renderer;

use Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Report\Api\Repository\EmailRepositoryInterface;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Mirasvit\Report\Api\Data\EmailInterface;

/**
 * @method EmailInterface getEmail()
 */
class Blocks extends Element
{
    /**
     * @var EmailRepositoryInterface
     */
    protected $emailRepository;

    /**
     * @var DateServiceInterface
     */
    protected $dateService;

    public function __construct(
        EmailRepositoryInterface $emailRepository,
        DateServiceInterface $dateService,
        Context $context
    ) {
        $this->emailRepository = $emailRepository;
        $this->dateService = $dateService;

        parent::__construct($context);
    }

    /**
     * @var string
     */
    private $template = 'Mirasvit_Report::email/edit/renderer/blocks.phtml';

    /**
     * {@inheritdoc}
     */
    public function getElementHtml()
    {
        $templateFile = $this->getTemplateFile($this->template);

        return $this->fetchView($templateFile);
    }

    /**
     * @return array
     */
    public function getAllBlocks()
    {
        $blocks = $this->emailRepository->getBlocks();
        foreach ($blocks as $idx => $block ) {
            $idxArray = explode('board:', $idx);
            if (3 === count($idxArray)) {
                $idxGroups[$idxArray[2]][$idx] = $block;
            } else {
                $idxGroups['Reports'][$idx] = $block;
            }
        }

        return $idxGroups;
    }

    /**
     * Weights for attributes
     *
     * @return array
     */
    public function getBlocks()
    {
        return $this->getEmail()->getBlocks();
    }

    /**
     * @return DateServiceInterface
     */
    public function getDateService()
    {
        return $this->dateService;
    }
}
