<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 */
namespace Forix\OrderComments\Plugin\Magento\Paypal\Model;

use Magento\Quote\Api\Data\PaymentInterface;

class Express{
    public function beforeAssignData(\Magento\Paypal\Model\Express $subject, $data)
    {
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        $subject->getInfoInstance()->setAdditionalInformation('comments', '');
        if (is_array($additionalData) && isset($additionalData['comments'])) {
            $subject->getInfoInstance()
                ->setAdditionalInformation('comments', $additionalData['comments']);
        }
        $subject->getInfoInstance()->setAdditionalInformation('ponumber', '');
        if (is_array($additionalData) && isset($additionalData['ponumber'])) {
            $subject->getInfoInstance()
                ->setAdditionalInformation('ponumber', $additionalData['ponumber']);
        }
        return [$data];
    }
}