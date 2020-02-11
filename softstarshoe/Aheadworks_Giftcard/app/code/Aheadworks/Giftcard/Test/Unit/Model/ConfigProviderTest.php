<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model;

use Aheadworks\Giftcard\Model\ConfigProvider;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigProviderTest
 * Test for \Aheadworks\Giftcard\Model\ConfigProvider
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model
 */
class ConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigProvider
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

        $this->object = $objectManager->getObject(
            ConfigProvider::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
            ]
        );
    }

    /**
     * Testing of getConfig method
     */
    public function testGetConfig()
    {
        $url = 'http://example.com/awgiftcard/cart/remove';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('awgiftcard/cart/remove')
            ->willReturn($url);

        $this->assertTrue(is_array($this->object->getConfig()));
    }
}
