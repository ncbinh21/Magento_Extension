<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model;

use Aheadworks\Giftcard\Api\Data\PoolInterface;
use Aheadworks\Giftcard\Api\Data\PoolSearchResultsInterface;
use Aheadworks\Giftcard\Model\Pool;
use Aheadworks\Giftcard\Model\PoolRepository;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Model\PoolFactory;
use Aheadworks\Giftcard\Api\Data\PoolInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Giftcard\Api\Data\PoolSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Collection as PoolCollection;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class PoolRepositoryTest
 * Test for \Aheadworks\Giftcard\Model\PoolRepository
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PoolRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PoolRepository
     */
    private $object;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var PoolFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $poolFactoryMock;

    /**
     * @var PoolInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $poolDataFactoryMock;

    /**
     * @var PoolSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var array
     */
    private $poolData = [
        'id' => 1,
        'name' => 'pool name'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['load', 'delete', 'save'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->poolFactoryMock = $this->getMockBuilder(PoolFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->poolDataFactoryMock = $this->getMockBuilder(PoolInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(PoolSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockBuilder(JoinProcessorInterface::class)
            ->getMockForAbstractClass();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            PoolRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'poolFactory' => $this->poolFactoryMock,
                'poolDataFactory' => $this->poolDataFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $poolMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->poolData['id']);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($poolMock);

        $this->assertSame($poolMock, $this->object->save($poolMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $poolMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->poolData['id']);

        $this->poolDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($poolMock, $this->poolData['id']);

        $this->assertSame($poolMock, $this->object->get($this->poolData['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if pool not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with poolId = 1
     */
    public function testGetOnExeption()
    {
        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $poolMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->poolDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolMock);

        $this->assertSame($poolMock, $this->object->get($this->poolData['id']));
    }

    /**
     * Testing of getList method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $filterName = 'Name';
        $filterValue = 'Sample Pool';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(PoolSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(PoolCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $poolModelMock = $this->getMockBuilder(Pool::class)
            ->setMethods(['getCollection', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $poolModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $poolModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->poolData);

        $this->poolFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, PoolInterface::class);

        $filterGroupMock = $this->getMockBuilder(FilterGroup::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock = $this->getMockBuilder(Filter::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);
        $filterMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filterMock->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $filterMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($filterValue);
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with([$filterName], [['eq' => $filterValue]]);
        $collectionMock
            ->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);
        $sortOrderMock->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $collectionMock->expects($this->once())
            ->method('addOrder')
            ->with($filterName, SortOrder::SORT_ASC);
        $sortOrderMock->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_ASC);
        $searchCriteriaMock->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($scCurrPage);
        $collectionMock->expects($this->once())
            ->method('setCurPage')
            ->with($scCurrPage)
            ->willReturn($collectionMock);
        $searchCriteriaMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($scPageSize);
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($scPageSize)
            ->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$poolModelMock]));

        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $this->poolDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($poolMock, $this->poolData, PoolInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$poolMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->object->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $poolMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->poolData['id']);

        $this->poolDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($poolMock, $this->poolData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($poolMock);

        $this->assertTrue($this->object->delete($poolMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $poolMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->poolData['id']);

        $this->poolDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($poolMock, $this->poolData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($poolMock);

        $this->assertTrue($this->object->deleteById($this->poolData['id']));
    }
}
