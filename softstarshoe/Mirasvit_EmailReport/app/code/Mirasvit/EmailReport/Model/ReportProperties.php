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



namespace Mirasvit\EmailReport\Model;


trait ReportProperties
{
    /**
     * {@inheritDoc}
     */
    public function getTriggerId()
    {
        return $this->getData(self::TRIGGER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setTriggerId($triggerId)
    {
        $this->setData(self::TRIGGER_ID, $triggerId);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueueId()
    {
        return $this->getData(self::QUEUE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setQueueId($queueId)
    {
        $this->setData(self::QUEUE_ID, $queueId);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionId()
    {
        return $this->getData(self::SESSION_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setSessionId($sessionId)
    {
        $this->setData(self::SESSION_ID, $sessionId);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }
}