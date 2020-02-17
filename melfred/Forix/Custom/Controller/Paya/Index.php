<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/10/2018
 * Time: 16:08
 */
namespace Forix\Custom\Controller\Paya;

use Magento\Framework\App\ResponseInterface;

/**
 * This controller face IP to request paya js.
 * Class Index
 * @method \Magento\Framework\App\Response\Http getResponse()
 * @package Forix\Custom\Controller\Paya
 */
class Index extends \Forix\Custom\Controller\IsRequestAbstract
{
    /**
     * @var string
     */
    protected $_jsPath = 'https://www.sagepayments.net/pay/1.0.1/js/pay.min.js';


    public function getJsPath()
    {
        /**
         * pay.min.js
         * build/Formatting.js
         * build/Request.js
         * build/Validation.js
         * build/Core.js
         * build/Response.js
         */
        //$file = $this->getRequest()->getParam('file');
        return $this->_jsPath;
    }
}