<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Block\Customer\Giftcard;

use Aheadworks\Giftcard\Api\GiftcardManagementInterface;
use Aheadworks\Giftcard\Block\Customer\Giftcard\Codes;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class CodesTest
 * Test for \Aheadworks\Giftcard\Block\Customer\Giftcard\Codes
 *
 * @package Aheadworks\Giftcard\Test\Unit\Block\Customer\Giftcard
 */
class CodesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Codes
     */
    private $object;

    /**
     * @var GiftcardManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardManagementMock;

    /**
     * @var CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSessionMock;

    /**
     * @var CheckoutSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrencyMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getControllerName']
        );
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'request' => $this->requestMock
            ]
        );
        $this->giftcardManagementMock = $this->getMockForAbstractClass(GiftcardManagementInterface::class);
        $this->customerSessionMock = $this->getMockBuilder(CustomerSession::class)
            ->setMethods(['isLoggedIn', 'getCustomer'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->checkoutSessionMock = $this->getMockBuilder(CheckoutSession::class)
            ->setMethods(['getQuoteId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrencyMock = $this->getMockForAbstractClass(PriceCurrencyInterface::class);

        $this->object = $objectManager->getObject(
            Codes::class,
            [
                'context' => $contextMock,
                'giftcardManagement' => $this->giftcardManagementMock,
                'customerSession' => $this->customerSessionMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'priceCurrency' => $this->priceCurrencyMock
            ]
        );
    }

    /**
     * Testing of isAccountPage method
     *
     * @param string $moduleName
     * @param string $controllerName
     * @param bool $expectedValue
     * @dataProvider isAccountPageDataProvider
     */
    public function testIsAccountPage($moduleName, $controllerName, $expectedValue)
    {
        $this->requestMock->expects($this->once())
            ->method('getModuleName')
            ->willReturn($moduleName);
        $this->requestMock->expects($this->once())
            ->method('getControllerName')
            ->willReturn($controllerName);

        $this->assertEquals($expectedValue, $this->object->isAccountPage());
    }

    /**
     * Data provider for testIsAccountPage
     *
     * @return []
     */
    public function isAccountPageDataProvider()
    {
        return [
            ['awgiftcard', 'card', true],
            ['awgiftcard', 'product', false]
        ];
    }

    /**
     * Testing of getCustomerGiftcardCodes method
     */
    public function testGetCustomerGiftcardCodes()
    {
        $quoteId = 1;
        $customerEmail = 'example@example.com';

        $this->customerSessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);

        $this->requestMock->expects($this->once())
            ->method('getModuleName')
            ->willReturn('checkout');
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $customerMock = $this->getMockBuilder(Customer::class)
            ->setMethods(['getEmail'])
            ->disableOriginalConstructor()
            ->getMock();
        $customerMock->expects($this->once())
            ->method('getEmail')
            ->willReturn($customerEmail);
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);
        $this->giftcardManagementMock->expects($this->once())
            ->method('getCustomerGiftcards')
            ->with($customerEmail, $quoteId)
            ->willReturn([]);

        $this->assertTrue(is_array($this->object->getCustomerGiftcardCodes()));
    }

    /**
     * Testing of formatPrice method
     */
    public function testFormatPrice()
    {
        $amount = 10;
        $expectedValue = '$10';

        $this->priceCurrencyMock->expects($this->once())
            ->method('format')
            ->with($amount)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->formatPrice($amount));
    }

    /**
     * Testing of getCheckCodeUrl method
     */
    public function testGetCheckCodeUrl()
    {
        $url = 'http://example.com/awgiftcard/card/checkCode';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('awgiftcard/card/checkCode')
            ->willReturn($url);

        $this->assertEquals($url, $this->object->getCheckCodeUrl());
    }

    /**
     * Testing of getApplyUrl method
     */
    public function testGetApplyUrl()
    {
        $url = 'http://example.com/awgiftcard/cart/apply';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('awgiftcard/cart/apply')
            ->willReturn($url);

        $this->assertEquals($url, $this->object->getApplyUrl());
    }
}
