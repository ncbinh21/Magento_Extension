<?php

namespace Forix\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Temando\Shipping\Api\Data\Checkout\AddressInterface;
use Temando\Shipping\Api\Data\Checkout\AddressInterfaceFactory;
use Temando\Shipping\Model\ResourceModel\Repository\AddressRepositoryInterface;

class SaveCheckoutFieldsObserver implements ObserverInterface
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * SaveCheckoutFieldsObserver constructor.
     * @param AddressRepositoryInterface $addressRepository
     * @param AddressInterfaceFactory $addressFactory
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        AddressInterfaceFactory $addressFactory
    ) {
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Api\Data\AddressInterface|\Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $observer->getData('quote_address');
        if ($quoteAddress->getAddressType() !== \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING) {
            return;
        }

        if (!$quoteAddress->getExtensionAttributes()) {
            return;
        }

        // persist checkout fields
        try {
            $checkoutAddress = $this->addressRepository->getByQuoteAddressId($quoteAddress->getId());
        } catch (NoSuchEntityException $e) {
            $checkoutAddress = $this->addressFactory->create(['data' => [
                AddressInterface::SHIPPING_ADDRESS_ID => $quoteAddress->getId(),
            ]]);
        }

        $extensionAttributes = $quoteAddress->getExtensionAttributes();
        if (is_array($extensionAttributes)){
            $serviceSelection = isset($extensionAttributes['checkout_fields']) ? $extensionAttributes['checkout_fields'] : [];
        } elseif (is_object($extensionAttributes)) {
            $serviceSelection = is_null($extensionAttributes->getCheckoutFields()) ? [] : $extensionAttributes->getCheckoutFields();
        }
        if(isset($serviceSelection)){
            $checkoutAddress->setServiceSelection($serviceSelection);
        }
        $this->addressRepository->save($checkoutAddress);
    }
}