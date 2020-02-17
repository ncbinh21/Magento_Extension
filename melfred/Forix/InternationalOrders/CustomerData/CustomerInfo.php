<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\InternationalOrders\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class CustomerInfo implements SectionSourceInterface
{
    /**
     * Persistent cookie name
     */
    const DOMESTIC_COOKIE = 'domestic-cookie';
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * CustomerInfo constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
    )
    {
        $this->customerRepository = $customerRepository;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $isDomestic = 0;
        if (!$this->currentCustomer->getCustomerId()) {
            if (isset($_COOKIE[self::DOMESTIC_COOKIE]) && $_COOKIE[self::DOMESTIC_COOKIE] == 1) {
                $isDomestic = 1;
            }
        } else {
            $customerId = $this->currentCustomer->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);
            if (!$customer->getCustomAttribute('country_name') || ($customer->getCustomAttribute('country_name') && $customer->getCustomAttribute('country_name')->getValue() == 'US')) {
                $isDomestic = 1;
                if (isset($_COOKIE[self::DOMESTIC_COOKIE])) {
                    setcookie(self::DOMESTIC_COOKIE, 1, time() + (86400 * 30), '/');
                }
            } else {
                setcookie(self::DOMESTIC_COOKIE, 0, time() + (86400 * 30), '/');
            }
        }
        return [
            'is_domestic' => $isDomestic
        ];
    }
}