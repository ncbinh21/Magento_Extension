<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model;

use Aheadworks\Giftcard\Model\Statistics;
use Aheadworks\Giftcard\Model\StatisticsFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Aheadworks\Giftcard\Model\ResourceModel\Statistics as ResourceStatistics;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class StatisticsTest
 * Test for \Aheadworks\Giftcard\Model\Statistics
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model
 */
class StatisticsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Statistics
     */
    private $object;

    /**
     * @var ResourceStatistics|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var StatisticsFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticsFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $contextMock = $objectManager->getObject(Context::class, []);
        $registryMock = $this->getMockBuilder(Registry::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->statisticsFactoryMock = $this->getMockBuilder(StatisticsFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder(ResourceStatistics::class)
            ->setMethods(['existsStatistics', 'save', 'setStoreId', 'load', 'getIdFieldName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            Statistics::class,
            [
                'context' => $contextMock,
                'registry' => $registryMock,
                'statisticsFactory' => $this->statisticsFactoryMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    /**
     * Testing of createStatistics method
     */
    public function testCreateStatistics()
    {
        $productId = 1;
        $storeId = 1;
        $existsStatisticsResult = false;

        $this->resourceMock->expects($this->once())
            ->method('existsStatistics')
            ->with($productId, $storeId)
            ->willReturn($existsStatisticsResult);
        $statisticsMock = $this->getMockBuilder(Statistics::class)
            ->setMethods(['setData'])
            ->disableOriginalConstructor()
            ->getMock();
        $statisticsMock->expects($this->once())
            ->method('setData')
            ->with(
                [
                    'product_id' => $productId,
                    'store_id' => $storeId
                ]
            )->willReturnSelf();
        $this->statisticsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticsMock);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($statisticsMock);

        $this->assertEquals($statisticsMock, $this->object->createStatistics($productId, $storeId));
    }

    /**
     * Testing of updateStatistics method
     */
    public function testUpdateStatistics()
    {
        $productId = 1;
        $storeId = 1;
        $data = [
            'qty_used' => 1
        ];
        $existsStatisticsResult = true;

        $this->resourceMock->expects($this->once())
            ->method('existsStatistics')
            ->with($productId, $storeId)
            ->willReturn($existsStatisticsResult);

        $statisticsMock = $this->getMockBuilder(Statistics::class)
            ->setMethods(['setData', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->statisticsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticsMock);
        $this->resourceMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($statisticsMock, $productId, 'product_id');

        $statisticsMock->expects($this->once())
            ->method('getData')
            ->with('qty_used')
            ->willReturn(0);
        $statisticsMock->expects($this->once())
            ->method('setData')
            ->with('qty_used', 1);

        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($statisticsMock);

        $this->object->updateStatistics($productId, $storeId, $data);
    }
}
