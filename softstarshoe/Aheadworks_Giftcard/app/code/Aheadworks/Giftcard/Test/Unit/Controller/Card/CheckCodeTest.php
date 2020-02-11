<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Controller\Card;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Controller\Card\CheckCode;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\View;
use Magento\Framework\View\Layout;
use Aheadworks\Giftcard\Block\Giftcard\Info as GiftcardInfo;

/**
 * Class CheckCodeTest
 * Test for \Aheadworks\Giftcard\Controller\Card\CheckCode
 *
 * @package Aheadworks\Giftcard\Controller\Card
 */
class CheckCodeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CheckCode
     */
    private $object;

    /**
     * @var GiftcardRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardRepositoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $escaperMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var View|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->giftcardRepositoryMock = $this->getMockForAbstractClass(GiftcardRepositoryInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);
        $this->escaperMock = $this->getMockBuilder(Escaper::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->responseMock = $this->getMockForAbstractClass(
            ResponseInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['appendBody']
        );
        $this->viewMock = $this->getMockBuilder(View::class)
            ->setMethods(['getLayout'])
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'response' => $this->responseMock,
                'view' => $this->viewMock
            ]
        );

        $this->object = $objectManager->getObject(
            CheckCode::class,
            [
                'context' => $contextMock,
                'giftcardRepository' => $this->giftcardRepositoryMock,
                'storeManager' => $this->storeManagerMock,
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
        $websiteId = 1;
        $expectedValue = 'html code';

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('code')
            ->willReturn($giftcardCode);

        $websiteMock = $this->getMockForAbstractClass(WebsiteInterface::class);
        $websiteMock->expects($this->once())
            ->method('getId')
            ->willReturn($websiteId);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsite')
            ->willReturn($websiteMock);

        $blockInstanceMock = $this->getMockBuilder(GiftcardInfo::class)
            ->setMethods(['toHtml', 'setGiftcard'])
            ->disableOriginalConstructor()
            ->getMock();
        $blockInstanceMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($expectedValue);
        $layoutMock = $this->getMockBuilder(Layout::class)
            ->setMethods(['createBlock'])
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(GiftcardInfo::class)
            ->willReturn($blockInstanceMock);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $this->giftcardRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($giftcardCode, $websiteId)
            ->willReturn($giftcardMock);
        $blockInstanceMock->expects($this->once())
            ->method('setGiftcard')
            ->with($giftcardMock);
        $this->responseMock->expects($this->once())
            ->method('appendBody')
            ->with($expectedValue);

        $this->object->execute();
    }
}
