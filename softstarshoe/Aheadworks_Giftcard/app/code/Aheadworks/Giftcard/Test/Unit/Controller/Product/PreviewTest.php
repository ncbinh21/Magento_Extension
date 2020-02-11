<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Controller\Product;

use Aheadworks\Giftcard\Controller\Product\Preview;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Action\Context;
use Aheadworks\Giftcard\Model\Email\Previewer;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json;

/**
 * Class PreviewTest
 * Test for \Aheadworks\Giftcard\Controller\Product\Preview
 *
 * @package Aheadworks\Giftcard\Controller\Product
 */
class PreviewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Preview
     */
    private $object;

    /**
     * @var JsonFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var Previewer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $previewerMock;

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
        $this->resultJsonFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->previewerMock = $this->getMockBuilder(Previewer::class)
            ->setMethods(['getPreview'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getPostValue']
        );

        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock
            ]
        );

        $this->object = $objectManager->getObject(
            Preview::class,
            [
                'context' => $contextMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'previewer' => $this->previewerMock
            ]
        );
    }

    /**
     * Testing of execute method
     */
    public function testExecute()
    {
        $storeId = 1;
        $productId = 1;
        $data = [];
        $content = 'content';

        $this->requestMock->expects($this->exactly(2))
            ->method('getParam')
            ->willReturnMap(
                [
                    ['store', null, $storeId],
                    ['product', null, $productId]
                ]
            );
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($data);
        $this->previewerMock->expects($this->once())
            ->method('getPreview')
            ->with($data, $storeId, $productId)
            ->willReturn($content);

        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->willReturnSelf();
        $this->resultJsonFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);

        $this->assertSame($resultJsonMock, $this->object->execute());
    }
}
