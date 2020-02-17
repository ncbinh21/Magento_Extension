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
 * @package   mirasvit/module-core
 * @version   1.2.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Block\Adminhtml;


use Magento\Backend\Block\Template;
use Mirasvit\Core\Api\Service\ValidationServiceInterface;
use Mirasvit\Core\Api\Service\ValidatorInterface;

class Validator extends Template
{
    protected $_template = 'Mirasvit_Core::validator.phtml';

    /**
     * @var ValidationServiceInterface
     */
    private $validationService;

    /**
     * @var bool
     */
    private $results = [];

    public function __construct(
        ValidationServiceInterface $validationService,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->validationService = $validationService;

        parent::__construct($context, $data);
    }

    /**
     * Get validation result.
     *
     * @return string[]
     */
    public function getResult()
    {
        if (!$this->results) {
            $module = $this->getRequest()->getParam('module');
            $this->results = $this->validationService->runValidation([$module]);
        }

        return $this->results;
    }

    /**
     * Whether a validation is passed or some tests fail.
     *
     * @return bool
     */
    public function getIsPassed()
    {
        foreach ($this->getResult() as $result) {
            if ($result[0] == ValidatorInterface::FAILED) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get label for status.
     *
     * @param int $status
     *
     * @return string
     */
    public function getStatusLabel($status)
    {
        $statusLabels = [
            ValidatorInterface::FAILED  => 'error',
            ValidatorInterface::WARNING => 'warning',
            ValidatorInterface::INFO    => 'info',
            ValidatorInterface::SUCCESS => 'success'
        ];

        return $statusLabels[$status];
    }
}