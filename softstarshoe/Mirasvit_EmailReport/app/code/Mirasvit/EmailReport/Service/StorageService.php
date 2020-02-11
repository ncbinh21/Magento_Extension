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



namespace Mirasvit\EmailReport\Service;

use Mirasvit\EmailReport\Api\Service\StorageServiceInterface;
use Mirasvit\EmailReport\Api\Service\CookieInterface;

class StorageService implements StorageServiceInterface
{
    /**
     * @var CookieInterface
     */
    private $cookie;

    /**
     * StorageService constructor.
     *
     * @param CookieInterface $cookie
     */
    public function __construct(CookieInterface $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * {@inheritDoc}
     */
    public function persistQueueId($queueId)
    {
        $this->cookie->set(self::QUEUE_PARAM_NAME, $queueId, 3600 * 3);
    }

    /**
     * @inheritDoc
     */
    public function retrieveQueueId()
    {
        return $this->cookie->get(self::QUEUE_PARAM_NAME);
    }
}
