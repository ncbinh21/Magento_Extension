<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardSearchResultsInterface;
use Aheadworks\Giftcard\Model\Giftcard;
use Aheadworks\Giftcard\Model\GiftcardRepository;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Model\GiftcardFactory;
use Aheadworks\Giftcard\Api\Data\GiftcardInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Collection as GiftcardCollection;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class GiftcardRepositoryTest
 * Test for \Aheadworks\Giftcard\Model\GiftcardRepository
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftcardRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GiftcardRepository
     */
    private $object;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var GiftcardFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardFactoryMock;

    /**
     * @var GiftcardInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardDataFactoryMock;

    /**
     * @var GiftcardSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

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
    private $giftcardData = [
        'id' => 1,
        'code' => 'gccode'
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
        $this->giftcardFactoryMock = $this->getMockBuilder(GiftcardFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardDataFactoryMock = $this->getMockBuilder(GiftcardInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(GiftcardSearchResultsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectHelperMock = $this->getMockBuilder(DataObjectHelper::class)
            ->setMethods(['populateWithArray'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataObjectProcessorMock = $this->getMockBuilder(DataObjectProcessor::class)
            ->setMethods(['buildOutputDataArray'])
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
            GiftcardRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'giftcardFactory' => $this->giftcardFactoryMock,
                'giftcardDataFactory' => $this->giftcardDataFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
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
        $giftcardModelMock = $this->getMockBuilder(Giftcard::class)
            ->setMethods(['setOrigData', 'getData', 'beforeSave'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardModelMock);
        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->giftcardData['id']);
        $giftcardMock->expects($this->once())
            ->method('getCode')
            ->willReturn($this->giftcardData['code']);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($giftcardModelMock, $this->giftcardData['id']);
        $giftcardModelMock->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($this->giftcardData);
        $giftcardModelMock->expects($this->once())
            ->method('setOrigData')
            ->with(null, $this->giftcardData);

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($giftcardMock, GiftcardInterface::class)
            ->willReturn($this->giftcardData);
        $this->dataObjectHelperMock->expects($this->at(0))
            ->method('populateWithArray')
            ->with($giftcardModelMock, $this->giftcardData, GiftcardInterface::class);

        $giftcardModelMock->expects($this->once())
            ->method('beforeSave');
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($giftcardModelMock);

        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock);

        $giftcardMock2 = $this->getMockForAbstractClass(GiftcardInterface::class);
        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock2);
        $this->dataObjectHelperMock->expects($this->at(1))
            ->method('populateWithArray')
            ->with($giftcardMock2, $this->giftcardData, GiftcardInterface::class);

        $this->assertSame($giftcardMock, $this->object->save($giftcardMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->giftcardData['id']);

        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($giftcardMock, $this->giftcardData['id']);

        $this->assertSame($giftcardMock, $this->object->get($this->giftcardData['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if giftcard not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with giftcardId = 1
     */
    public function testGetOnExeption()
    {
        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock);

        $this->assertSame($giftcardMock, $this->object->get($this->giftcardData['id']));
    }

    /**
     * Testing of getList method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $filterName = 'Name';
        $filterValue = 'Sample Giftcard';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(GiftcardSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->getMockBuilder(GiftcardCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $giftcardModelMock = $this->getMockBuilder(Giftcard::class)
            ->setMethods(['getCollection', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();
        $giftcardModelMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $giftcardModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->giftcardData);

        $this->giftcardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardModelMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, GiftcardInterface::class);

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
        $filterMock->expects($this->exactly(4))
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
            ->willReturn(new \ArrayIterator([$giftcardModelMock]));

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($giftcardMock, $this->giftcardData, GiftcardInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$giftcardMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->object->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->giftcardData['id']);

        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($giftcardMock, $this->giftcardData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($giftcardMock);

        $this->assertTrue($this->object->delete($giftcardMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->giftcardData['id']);

        $this->giftcardDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($giftcardMock, $this->giftcardData['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($giftcardMock);

        $this->assertTrue($this->object->deleteById($this->giftcardData['id']));
    }
}
