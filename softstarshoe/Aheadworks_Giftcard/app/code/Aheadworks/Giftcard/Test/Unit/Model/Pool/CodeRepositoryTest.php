<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model\Pool;

use Aheadworks\Giftcard\Api\Data\Pool\CodeInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeSearchResultsInterface;
use Aheadworks\Giftcard\Model\Pool\Code;
use Aheadworks\Giftcard\Model\Pool\CodeRepository;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Model\Pool\CodeFactory;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Code\Collection as PoolCodeCollection;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CodeRepositoryTest
 * Test for \Aheadworks\Giftcard\Model\Pool\CodeRepository
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model\Pool
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CodeRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CodeRepository
     */
    private $object;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var CodeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeFactoryMock;

    /**
     * @var CodeInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeDataFactoryMock;

    /**
     * @var CodeSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
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
    private $codeData = [
        'id' => 1,
        'code' => 'poolcode'
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
        $this->codeFactoryMock = $this->getMockBuilder(CodeFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->codeDataFactoryMock = $this->getMockBuilder(CodeInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(CodeSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionAttributesJoinProcessorMock = $this->getMockBuilder(JoinProcessorInterface::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            CodeRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'codeFactory' => $this->codeFactoryMock,
                'codeDataFactory' => $this->codeDataFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock
            ]
        );
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $codeMock = $this->getMockForAbstractClass(CodeInterface::class);
        $codeMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->codeData['id']);

        $this->codeDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($codeMock, $this->codeData['id']);

        $this->assertSame($codeMock, $this->object->get($this->codeData['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if code not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with codeId = 1
     */
    public function testGetOnExeption()
    {
        $codeMock = $this->getMockForAbstractClass(CodeInterface::class);
        $codeMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->codeDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeMock);

        $this->assertSame($codeMock, $this->object->get($this->codeData['id']));
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
        $searchResultsMock = $this->getMockForAbstractClass(CodeSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(PoolCodeCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $codeModelMock = $this->getMockBuilder(Code::class)
            ->setMethods(['getCollection', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $codeModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $codeModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->codeData);

        $this->codeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, CodeInterface::class);

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
            ->willReturn(new \ArrayIterator([$codeModelMock]));

        $codeMock = $this->getMockForAbstractClass(CodeInterface::class);
        $this->codeDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($codeMock, $this->codeData, CodeInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$codeMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->object->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $codeMock = $this->getMockForAbstractClass(CodeInterface::class);
        $codeMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->codeData['id']);

        $this->codeDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($codeMock, $this->codeData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($codeMock);

        $this->assertTrue($this->object->delete($codeMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $codeMock = $this->getMockForAbstractClass(CodeInterface::class);
        $codeMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->codeData['id']);

        $this->codeDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($codeMock, $this->codeData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($codeMock);

        $this->assertTrue($this->object->deleteById($this->codeData['id']));
    }
}
