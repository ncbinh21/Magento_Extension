<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Block\Giftcard;

use Aheadworks\Giftcard\Block\Giftcard\Info;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Message\Collection as MessageCollection;

/**
 * Class InfoTest
 * Test for \Aheadworks\Giftcard\Block\Giftcard\Info
 *
 * @package Aheadworks\Giftcard\Test\Unit\Block\Giftcard
 */
class InfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Info
     */
    private $object;

    /**
     * @var Status|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sourceStatusMock;

    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrencyMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->sourceStatusMock = $this->getMockBuilder(Status::class)
            ->setMethods(['getOptionByValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrencyMock = $this->getMockForAbstractClass(PriceCurrencyInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->localeDateMock = $this->getMockForAbstractClass(TimezoneInterface::class);

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'localeDate' => $this->localeDateMock
            ]
        );
        $this->object = $objectManager->getObject(
            Info::class,
            [
                'context' => $contextMock,
                'sourceStatus' => $this->sourceStatusMock,
                'priceCurrency' => $this->priceCurrencyMock,
                'messageManager' => $this->messageManagerMock
            ]
        );
    }

    /**
     * Testing of formatPrice method
     */
    public function testFormatPrice()
    {
        $amount = 10;
        $expectedValue = '$10';

        $this->priceCurrencyMock->expects($this->once())
            ->method('convertAndFormat')
            ->with($amount)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->formatPrice($amount));
    }

    /**
     * Testing of formatState method
     */
    public function testFormatState()
    {
        $state = Status::ACTIVE;
        $expectedValue = 'Active';

        $this->sourceStatusMock->expects($this->once())
            ->method('getOptionByValue')
            ->with($state)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->formatState($state));
    }

    /**
     * Testing of getMessages method
     */
    public function testGetMessages()
    {
        $state = Status::ACTIVE;
        $expectedValue = [];

        $messageCollection = $this->getMockBuilder(MessageCollection::class)
            ->setMethods(['getItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $messageCollection->expects($this->once())
            ->method('getItems')
            ->willReturn($expectedValue);
        $this->messageManagerMock->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($messageCollection);

        $this->assertEquals($expectedValue, $this->object->getMessages($state));
    }
}
