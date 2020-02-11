<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Observer;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface;
use Aheadworks\Giftcard\Observer\OrderCreationProcessDataObserver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Framework\Escaper;
use Magento\Framework\Message\ManagerInterface;
use Aheadworks\Giftcard\Api\GiftcardCartManagementInterface;
use Magento\Sales\Model\AdminOrder\Create as AdminOrderCreate;

/**
 * Class OrderCreationProcessDataObserverTest
 * Test for \Aheadworks\Giftcard\Observer\OrderCreationProcessDataObserver
 *
 * @package Aheadworks\Giftcard\Test\Unit\Observer
 */
class OrderCreationProcessDataObserverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderCreationProcessDataObserver
     */
    private $object;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardCartManagementMock;

    /**
     * @var GiftcardCartManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->giftcardCartManagementMock = $this->getMockForAbstractClass(GiftcardCartManagementInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            OrderCreationProcessDataObserver::class,
            [
                'giftcardCartManagement' => $this->giftcardCartManagementMock,
                'messageManager' => $this->messageManagerMock,
                'escaper' => $this->escaperMock
            ]
        );
    }

    /**
     * Testing of apply Gift Card code
     */
    public function testApplyGiftcardCode()
    {
        $quoteId = 1;
        $giftcardCode = 'gccode';
        $request = [
            'aw_giftcard_apply' => $giftcardCode
        ];

        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $adminOrderMock = $this->getMockBuilder(AdminOrderCreate::class)
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
        $adminOrderMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $eventMock = $this->getMockBuilder(Event::class)
            ->setMethods(['getOrderCreateModel', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getOrderCreateModel')
            ->willReturn($adminOrderMock);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->giftcardCartManagementMock->expects($this->once())
            ->method('set')
            ->with($quoteId, $giftcardCode, false);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->willReturnSelf();

        $this->object->execute($observerMock);
    }

    /**
     * Testing of remove Gift Card code
     */
    public function testRemoveGiftcardCode()
    {
        $quoteId = 1;
        $giftcardCode = 'gccode';
        $request = [
            'aw_giftcard_remove' => $giftcardCode
        ];

        $quoteMock = $this->getMockForAbstractClass(QuoteInterface::class);
        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn($quoteId);
        $adminOrderMock = $this->getMockBuilder(AdminOrderCreate::class)
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();
        $adminOrderMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $eventMock = $this->getMockBuilder(Event::class)
            ->setMethods(['getOrderCreateModel', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('getOrderCreateModel')
            ->willReturn($adminOrderMock);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $observerMock = $this->getMockBuilder(Observer::class)
            ->setMethods(['getEvent'])
            ->disableOriginalConstructor()
            ->getMock();
        $observerMock->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($eventMock);

        $this->giftcardCartManagementMock->expects($this->once())
            ->method('remove')
            ->with($quoteId, $giftcardCode, false);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->willReturnSelf();

        $this->object->execute($observerMock);
    }
}
