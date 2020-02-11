<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Observer;

use Aheadworks\Giftcard\Observer\QuoteSubmitBeforeObserver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Quote\Api\Data\CartExtensionInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterfaceFactory as GiftcardOrderInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface as GiftcardOrderInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class QuoteSubmitBeforeObserverTest
 * Test for \Aheadworks\Giftcard\Observer\QuoteSubmitBeforeObserver
 *
 * @package Aheadworks\Giftcard\Test\Unit\Observer
 */
class QuoteSubmitBeforeObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var QuoteSubmitBeforeObserver
     */
    private $object;

    /**
     * @var OrderExtensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderExtensionFactoryMock;

    /**
     * @var GiftcardOrderInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardOrderFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->orderExtensionFactoryMock = $this->getMockBuilder(OrderExtensionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardOrderFactoryMock = $this->getMockBuilder(GiftcardOrderInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            QuoteSubmitBeforeObserver::class,
            [
                'orderExtensionFactory' => $this->orderExtensionFactoryMock,
                'giftcardOrderFactory' => $this->giftcardOrderFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $baseAwGiftcardAmount = 10;
        $awGiftcardAmount = 10;
        $quoteData = [
            'id' => 1
        ];

        $orderMock = $this->getMockForAbstractClass(
            OrderInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setBaseAwGiftcardAmount', 'setAwGiftcardAmount']
        );
        $quoteMock = $this->getMockForAbstractClass(
            CartInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getBaseAwGiftcardAmount', 'getAwGiftcardAmount']
        );

        $eventMock = $this->getMockBuilder(Event::class)
            ->setMethods(['getOrder', 'getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $eventMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $quoteMock->expects($this->exactly(3))
            ->method('getBaseAwGiftcardAmount')
            ->willReturn($baseAwGiftcardAmount);
        $quoteMock->expects($this->once())
            ->method('getAwGiftcardAmount')
            ->willReturn($awGiftcardAmount);

        $orderMock->expects($this->once())
            ->method('setBaseAwGiftcardAmount')
            ->with($baseAwGiftcardAmount)
            ->willReturnSelf();
        $orderMock->expects($this->once())
            ->method('setAwGiftcardAmount')
            ->with($awGiftcardAmount)
            ->willReturnSelf();
        $orderMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(null);

        $orderExtensionMock = $this->getMockForAbstractClass(
            OrderExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setAwGiftcardCodes']
        );
        $this->orderExtensionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderExtensionMock);

        $quoteExtensionMock = $this->getMockForAbstractClass(
            CartExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getAwGiftcardCodes']
        );
        $quoteMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($quoteExtensionMock);

        $quoteGiftcardMock = $this->getMockForAbstractClass(
            GiftcardQuoteInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getData']
        );
        $quoteGiftcardMock->expects($this->once())
            ->method('getData')
            ->willReturn($quoteData);
        $quoteExtensionMock->expects($this->once())
            ->method('getAwGiftcardCodes')
            ->willReturn([$quoteGiftcardMock]);

        $orderGiftcardMock = $this->getMockForAbstractClass(GiftcardOrderInterface::class);
        $this->giftcardOrderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderGiftcardMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($orderGiftcardMock, $quoteData, GiftcardOrderInterface::class)
            ->willReturnSelf();

        $orderGiftcardMock->expects($this->once())
            ->method('setId')
            ->with(null);

        $orderExtensionMock->expects($this->once())
            ->method('setAwGiftcardCodes')
            ->with([$orderGiftcardMock]);
        $orderMock->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($orderExtensionMock);

        $this->object->execute($observerMock);
    }

    /**
     * Testing of execute method if no gifcard applied (Paypal Express compatibility)
     */
    public function testExecuteNoGiftCardApplied()
    {
        $baseAwGiftcardAmount = 0;

        $orderMock = $this->getMockForAbstractClass(
            OrderInterface::class,
            [],
            '',
            true,
            true,
            true,
            []
        );
        $quoteMock = $this->getMockForAbstractClass(
            CartInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getBaseAwGiftcardAmount']
        );

        $eventMock = $this->getMockBuilder(Event::class)
            ->setMethods(['getOrder', 'getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $eventMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $quoteMock->expects($this->once())
            ->method('getBaseAwGiftcardAmount')
            ->willReturn($baseAwGiftcardAmount);

        $this->object->execute($observerMock);
    }
}
