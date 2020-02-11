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
 * @package   mirasvit/module-event
 * @version   1.1.10
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\Event\Admin;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use Mirasvit\Event\Event\Context;
use Mirasvit\Event\Event\ObservableEvent;
use Mirasvit\Event\EventData\AdminData;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class LoginEvent extends ObservableEvent
{
    const IDENTIFIER_LOGIN  = 'admin_login';
    const IDENTIFIER_FAILED = 'admin_failed';

    const PARAM_USER_ID   = 'user_id';
    const PARAM_LOGDATE   = 'logdate';
    const PARAM_USER_NAME = 'username';
    const PARAM_IS_LOGGED = 'is_logged_in';
    const PARAM_IP        = 'ip';

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        DateTime $dateTime,
        UserFactory $userFactory,
        RemoteAddress $remoteAddress,
        Context $context
    ) {
        parent::__construct($context);

        $this->remoteAddress = $remoteAddress;
        $this->userFactory = $userFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            self::IDENTIFIER_LOGIN  => __('Admin / Logged In'),
            self::IDENTIFIER_FAILED => __('Admin / Login Failed'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEventData()
    {
        return [
            $this->context->get(AdminData::class),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function expand($params)
    {
        $admin = $this->context->create(AdminData::class)->load($params[self::PARAM_USER_ID]);

        $params[AdminData::IDENTIFIER] = $admin;

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function toString($params)
    {
        if ($params[self::PARAM_IS_LOGGED]) {
            $string = '%1 logged in admin panel at %2';
        } else {
            $string = '%1 failed to log in admin panel at %2';
        }

        return __($string, $params[self::PARAM_USER_NAME], $params[self::PARAM_LOGDATE]);
    }

    /**
     * @param User     $subject
     * @param callable $proceed
     * @param string   $username
     * @param string[] $args
     *
     * @return bool
     */
    public function aroundAuthenticate(User $subject, callable $proceed, $username, ...$args)
    {
        // call original method
        $result = $proceed($username, ...$args);

        // collect the params after the original method gets called
        /** @var User $user */
        $user = $this->userFactory->create()->loadByUsername($username);
        $params = [
            self::PARAM_USER_NAME    => $username,
            self::PARAM_USER_ID      => $user->getId(),
            self::PARAM_LOGDATE      => $this->dateTime->gmtDate(),
            self::PARAM_IP           => $this->remoteAddress->getRemoteAddress(),
            self::PARAM_IS_LOGGED    => $result,
            self::PARAM_EXPIRE_AFTER => 1,
        ];

        // finally register an event
        $this->context->eventRepository->register(
            $result ? self::IDENTIFIER_LOGIN : self::IDENTIFIER_FAILED,
            [$params[self::PARAM_USER_ID]],
            $params
        );

        return $result;
    }
}
