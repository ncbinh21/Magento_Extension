<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Block\Checkout\Cart;

use Aheadworks\Giftcard\Block\Checkout\Cart\Giftcard;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class GiftcardTest
 * Test for \Aheadworks\Giftcard\Block\Checkout\Cart\Giftcard
 *
 * @package Aheadworks\Giftcard\Test\Unit\Block\Checkout\Cart
 */
class GiftcardTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Giftcard
     */
    private $object;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'urlBuilder' => $this->urlBuilderMock
            ]
        );

        $this->object = $objectManager->getObject(
            Giftcard::class,
            [
                'context' => $contextMock
            ]
        );
    }

    /**
     * Testing of getActionUrl method
     */
    public function testGetActionUrl()
    {
        $url = 'http://example.com/awgiftcard/cart/apply';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('awgiftcard/cart/apply')
            ->willReturn($url);

        $this->assertEquals($url, $this->object->getActionUrl());
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
}
