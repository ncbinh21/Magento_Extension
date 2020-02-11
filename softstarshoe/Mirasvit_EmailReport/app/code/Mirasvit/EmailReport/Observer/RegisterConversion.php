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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\EmailReport\Api\Service\StorageServiceInterface;
use Mirasvit\EmailReport\Api\Service\RegistrarInterface;

class RegisterConversion implements ObserverInterface
{
    /**
     * @var array
     */
    private $registrars;
    /**
     * @var StorageServiceInterface
     */
    private $storageService;

    /**
     * RegisterConversion constructor.
     *
     * @param StorageServiceInterface $storageService
     * @param RegistrarInterface[]     $registrars
     */
    public function __construct(
        StorageServiceInterface $storageService,
        $registrars = []
    ) {
        $this->storageService = $storageService;
        $this->registrars = $registrars;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($queueId = $this->storageService->retrieveQueueId()) {
            $object = $observer->getData('object');
            foreach ($this->registrars as $registrar) {
                $registrar->register($object, $queueId);
            }
        }
    }
}
