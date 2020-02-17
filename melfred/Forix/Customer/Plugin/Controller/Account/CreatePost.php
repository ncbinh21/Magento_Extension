<?php

namespace Forix\Customer\Plugin\Controller\Account;

class CreatePost
{
    /**
     * @param \Magento\Customer\Controller\Account\CreatePost $subject
     */
    public function beforeExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject
    ) {
        $fullName = trim($subject->getRequest()->getParam('fullname'));
        $len = strlen($fullName);
        $pos = strpos($fullName, ' ');
        $end = $len - $pos;
        $firstName = substr($fullName, 0, $pos);
        $lastname = substr($fullName, $pos, $end);
        $params = $subject->getRequest()->getParams();
        $params['firstname'] = trim($firstName);
        $params['lastname'] = trim($lastname);
        $subject->getRequest()->setParams($params);
    }
}