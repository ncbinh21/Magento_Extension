<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Observer;

use Aheadworks\Giftcard\Observer\AddPaymentItemObserver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Payment\Model\Cart;
use Magento\Payment\Model\Cart\SalesModel\SalesModelInterface;

/**
 * Class AddPaymentItemObserverTest
 * Test for \Aheadworks\Giftcard\Observer\AddPaymentItemObserver
 *
 * @package Aheadworks\Giftcard\Test\Unit\Observer
 */
class AddPaymentItemObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AddPaymentItemObserver
     */
    private $object;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->object = $objectManager->getObject(
            AddPaymentItemObserver::class,
            []
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $baseGiftCardAmount = 10;

        $salesModelMock = $this->getMockForAbstractClass(SalesModelInterface::class);
        $cartMock = $this->getMockBuilder(Cart::class)
            ->setMethods(['getSalesModel', 'addDiscount'])
            ->disableOriginalConstructor()
            ->getMock();
        $cartMock->expects($this->once())
            ->method('getSalesModel')
            ->willReturn($salesModelMock);
        $eventMock = $this->getMockBuilder(Event::class)
            ->setMethods(['getCart'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getCart')
            ->willReturn($cartMock);
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        $salesModelMock->expects($this->once())
            ->method('getDataUsingMethod')
            ->with('base_aw_giftcard_amount')
            ->willReturn($baseGiftCardAmount);
        $cartMock->expects($this->once())
            ->method('addDiscount')
            ->with($baseGiftCardAmount);

        $this->object->execute($observerMock);
    }
}
