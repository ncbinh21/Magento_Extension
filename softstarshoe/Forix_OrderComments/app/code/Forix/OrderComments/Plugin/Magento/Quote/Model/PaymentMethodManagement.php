<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStarShoes
 */
namespace Forix\OrderComments\Plugin\Magento\Quote\Model;

class PaymentMethodManagement
{
    public function aroundImportData(\Magento\Quote\Model\Quote\Payment $subject, $proceed, $data) {
        $quote = $subject->getQuote();
        $quote->getPayment()->setAdditionalInformation('comments', '');
        if(isset($data['additional_data']['comments'])) {
            $quote->getPayment()->setAdditionalInformation('comments', $data['additional_data']['comments']);
        }
        return $proceed($data);
    }
}