<?php
namespace Forix\CustomAddress\Plugin\Magento\Quote\Model\Quote\Address;

class BillingAddressPersister
{

    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function beforeSave(
        \Magento\Quote\Model\Quote\Address\BillingAddressPersister $subject,
        $quote,
        \Magento\Quote\Api\Data\AddressInterface $address,
        $useForShipping = false
    ) {

        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {
            try {
                $address->setLicenseNumber($extAttributes->getLicenseNumber());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}