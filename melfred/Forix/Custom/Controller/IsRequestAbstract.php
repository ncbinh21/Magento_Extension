<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/10/2018
 * Time: 16:15
 */

namespace Forix\Custom\Controller;
abstract class IsRequestAbstract extends \Magento\Framework\App\Action\Action
{
    public abstract function getJsPath();

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($path = $this->getJsPath()) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $path);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            $this->getResponse()->setHeader('Content-type', 'application/javascript; charset=UTF-8');
            return $this->getResponse()->setBody($data);
        }
        $this->_redirect('noroute');
        return;
    }
}