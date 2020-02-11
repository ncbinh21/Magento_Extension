<?php
/**
 * Created by PhpStorm.
 * User: nghiata
 * Date: 13/11/2017
 * Time: 10:35
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml\Token;


use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Yosto\InstagramConnect\Helper\InstagramClient;

class Get extends Action
{
    protected $_instagramConnectHelper;
    public function __construct(
        InstagramClient $instagramConnectHelper,
        Action\Context $context
    ) {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        parent::__construct($context);
    }


    public function execute()
    {
        $request = $this->getRequest();
        $clientId = $request->getPost('client_id');
        $clientSecret = $request->getPost('client_secret');
        $code = $request->getPost('code');
        $redirectUri = $this->_instagramConnectHelper->getBaseUrl();
        $accessToken = $this->_instagramConnectHelper->getOAuthTokenFromAjaxPost(
            $code,
            $clientId,
            $clientSecret,
            $redirectUri
        );

        $this->getResponse()->setBody($accessToken);
    }

}