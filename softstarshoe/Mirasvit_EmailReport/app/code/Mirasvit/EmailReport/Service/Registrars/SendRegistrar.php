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



namespace Mirasvit\EmailReport\Service\Registrars;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\EmailReport\Api\Repository\EmailRepositoryInterface;
use Mirasvit\EmailReport\Api\Service\RegistrarInterface;

class SendRegistrar implements RegistrarInterface
{
    /**
     * OrderRegistrar constructor.
     *
     * @param EmailRepositoryInterface    $emailRepository
     */
    public function __construct(
        EmailRepositoryInterface $emailRepository
    ) {
        $this->emailRepository = $emailRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function register(AbstractModel $model,  $queueId)
    {
        if ($model instanceof QueueInterface && $model->getId()
            && $model->getTrigger() && $model->getTrigger()->getId()
        ) {
            $email = $this->emailRepository->create()
                ->setTriggerId($model->getTriggerId())
                ->setQueueId($queueId);

            $this->emailRepository->save($email);
        }
    }
}
