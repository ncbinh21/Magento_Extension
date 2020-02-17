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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Authorization\Model\Acl\AclRetriever;
use Magento\Backend\Model\Auth\Session;

class MenuChange implements ObserverInterface
{
    /**
     * @var AclRetriever
     */
    protected $aclRetriever;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @param AclRetriever $aclRetriever
     * @param Session $authSession
     */
    public function __construct(
        AclRetriever $aclRetriever,
        Session $authSession
    ) {
        $this->aclRetriever = $aclRetriever;
        $this->authSession = $authSession;
    }

    /**
     * @param string $body
     * @return bool
     */
    protected function hasDoctype($body)
    {
        if (stripos($body, '<!doctype html') === 0) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllowedResources()
    {
        $user = $this->authSession->getUser();
        if (!$user) {
            return false;
        }
        $role = $user->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());

        return $resources;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getResponse();
        $body = $response->getBody();

        if (($allowedResources = $this->getAllowedResources())
            && isset($allowedResources[0])
            && $allowedResources[0] != 'Magento_Backend::all'
            && $this->hasDoctype(trim($body))) {
                preg_match('/SEO \\&amp; Search(.*?)\\<\\/div\\>/ims', $body, $matches);
            if (isset($matches[0]) && strpos($matches[0], '<ul role="menu" ></ul>') !== false) {
                $body = preg_replace('/class="item-marketing-seo/',
                    ' style="display:none;" class="item-marketing-seo',
                    $body);
                $response->setBody($body);
            }
        }
    }
}
