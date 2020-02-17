<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\Custom\Rewrite\Magento\AdminGws\Observer;


class ValidateModelSaveBefore extends \Magento\AdminGws\Observer\ValidateModelSaveBefore
{
    public function __construct(
        \Magento\AdminGws\Model\Role $role,
        \Magento\AdminGws\Model\CallbackInvoker $callbackInvoker,
        \Magento\AdminGws\Model\ConfigInterface $config
    ) {
        $this->callbackInvoker = $callbackInvoker;
        $this->role = $role;
        $this->config = $config;
    }
}