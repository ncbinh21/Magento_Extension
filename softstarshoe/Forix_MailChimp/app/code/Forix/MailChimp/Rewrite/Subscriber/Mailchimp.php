<?php
namespace Forix\MailChimp\Rewrite\Subscriber;

class Mailchimp extends \Mailchimp
{
    public function call($url,$params,$method=Mailchimp::GET)
    {

        if(count($params)&&$method!=Mailchimp::GET)
        {
            $params = json_encode($params);
        }

        $ch = $this->_ch;
        if(count($params)&&$method!=Mailchimp::GET)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        else {
            if (count($params)) {
                $_params = http_build_query($params);
                $url .= '?' . $_params;
            }
        }
        curl_setopt($ch, CURLOPT_URL, $this->_root . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_VERBOSE, $this->_debug);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,$method);


        $response_body = curl_exec($ch);

        $info = curl_getinfo($ch);
        if(curl_error($ch)) {
            throw new \Mailchimp_HttpError("API call to $url failed: " . curl_error($ch));
        }
        $result = json_decode($response_body, true);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->create('\Magento\Framework\App\RequestInterface');
        $_storeBlog = $request->getParam('store_name');
        /*if(floor($info['http_code'] / 100) >= 4 && $_storeBlog != "blog") {
            throw new \Mailchimp_Error($result['title'].' : '.$result['detail']);
        }*/
//print_r($result);exit;
        return $result;
    }
}