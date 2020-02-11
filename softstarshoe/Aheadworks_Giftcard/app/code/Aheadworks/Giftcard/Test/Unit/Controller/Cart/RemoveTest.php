<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Controller\Cart;

use Aheadworks\Giftcard\Api\GiftcardCartManagementInterface;
use Aheadworks\Giftcard\Controller\Cart\Remove;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class RemoveTest
 * Test for \Aheadworks\Giftcard\Controller\Cart\Remove
 *
 * @package Aheadworks\Giftcard\Controller\Cart
 */
class RemoveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Remove
     */
    private $object;

    /**
     * @var GiftcardCartManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardCartManagementMock;

    /**
     * @var CheckoutSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

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
        $this->giftcardCartManagementMock = $this->getMockForAbstractClass(GiftcardCartManagementInterface::class);
        $this->checkoutSessionMock = $this->getMockBuilder(CheckoutSession::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock
            ]
        );

        $this->object = $objectManager->getObject(
            Remove::class,
            [
                'context' => $contextMock,
                'giftcardCartManagement' => $this->giftcardCartManagementMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'escaper' => $this->escaperMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $giftcardCode = 'gccode';
        $quoteId = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('code')
            ->willReturn($giftcardCode);
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->giftcardCartManagementMock->expects($this->once())
            ->method('remove')
            ->with($quoteId, $giftcardCode)
            ->willReturn(true);

        $this->object->execute();
    }
}
