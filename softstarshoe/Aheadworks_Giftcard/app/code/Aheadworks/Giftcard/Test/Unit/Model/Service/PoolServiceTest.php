<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model\Service;

use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeSearchResultsInterface as PoolCodeSearchResultsInterface;
use Aheadworks\Giftcard\Api\Data\PoolInterface;
use Aheadworks\Giftcard\Model\Service\PoolService;
use Aheadworks\Giftcard\Model\Source\YesNo;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Giftcard\Model\Giftcard\CodeGenerator;
use Aheadworks\Giftcard\Api\PoolCodeRepositoryInterface;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Api\PoolRepositoryInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterface as PoolCodeInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterfaceFactory as PoolCodeInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterfaceFactory;
use Aheadworks\Giftcard\Model\Import\PoolCode as ImportPoolCode;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class PoolServiceTest
 * Test for \Aheadworks\Giftcard\Model\Service\PoolService
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model\Service
 */
class PoolServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PoolService
     */
    private $object;

    /**
     * CodeGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeGeneratorMock;

    /**
     * PoolCodeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $poolCodeRepositoryMock;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * PoolRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $poolRepositoryMock;

    /**
     * PoolCodeInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $poolCodeFactoryMock;

    /**
     * @var CodeGenerationSettingsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeGenerationSettingsFactoryMock;

    /**
     * ImportPoolCode|\PHPUnit_Framework_MockObject_MockObject
     */
    private $importPoolCodeMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SortOrderBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sortOrderBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->codeGeneratorMock = $this->getMockBuilder(CodeGenerator::class)
            ->setMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->poolCodeRepositoryMock = $this->getMockForAbstractClass(PoolCodeRepositoryInterface::class);
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->poolRepositoryMock = $this->getMockForAbstractClass(PoolRepositoryInterface::class);
        $this->poolCodeFactoryMock = $this->getMockBuilder(PoolCodeInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->codeGenerationSettingsFactoryMock = $this->getMockBuilder(CodeGenerationSettingsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->importPoolCodeMock = $this->getMockBuilder(ImportPoolCode::class)
            ->setMethods(['process'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['create', 'addFilter', 'setCurrentPage', 'setPageSize', 'addSortOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sortOrderBuilderMock = $this->getMockBuilder(SortOrderBuilder::class)
            ->setMethods(['setField', 'setDirection', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            PoolService::class,
            [
                'codeGenerator' => $this->codeGeneratorMock,
                'poolCodeRepository' => $this->poolCodeRepositoryMock,
                'entityManager' => $this->entityManagerMock,
                'poolRepository' => $this->poolRepositoryMock,
                'poolCodeFactory' => $this->poolCodeFactoryMock,
                'codeGenerationSettingsFactory' => $this->codeGenerationSettingsFactoryMock,
                'importPoolCode' => $this->importPoolCodeMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'sortOrderBuilder' => $this->sortOrderBuilderMock
            ]
        );
    }

    /**
     * Testing of generateCodesForPool method
     */
    public function testGenerateCodesForPool()
    {
        $poolData = [
            'pool_id' => 1,
            'code_length' => 12,
            'code_format' => 'numeric',
            'code_prefix' => null,
            'code_suffix' => null,
            'code_delimiter_at_every' => 2
        ];
        $qty = 1;
        $poolCode = 'poolcode';

        $poolMock = $this->getMockForAbstractClass(PoolInterface::class);
        $poolMock->expects($this->once())
            ->method('getCodeLength')
            ->willReturn($poolData['code_length']);
        $poolMock->expects($this->once())
            ->method('getCodeFormat')
            ->willReturn($poolData['code_format']);
        $poolMock->expects($this->once())
            ->method('getCodePrefix')
            ->willReturn($poolData['code_prefix']);
        $poolMock->expects($this->once())
            ->method('getCodeSuffix')
            ->willReturn($poolData['code_suffix']);
        $poolMock->expects($this->once())
            ->method('getCodeDelimiterAtEvery')
            ->willReturn($poolData['code_delimiter_at_every']);

        $this->poolRepositoryMock->expects($this->once())
            ->method('get')
            ->with($poolData['pool_id'])
            ->willReturn($poolMock);
        $codeGenerationSettingsMock = $this->getMockForAbstractClass(CodeGenerationSettingsInterface::class);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setQty')
            ->with($qty)
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setLength')
            ->with($poolData['code_length'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setFormat')
            ->with($poolData['code_format'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setPrefix')
            ->with($poolData['code_prefix'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setSuffix')
            ->with($poolData['code_suffix'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setDelimiterAtEvery')
            ->with($poolData['code_delimiter_at_every'])
            ->willReturnSelf();

        $this->codeGenerationSettingsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeGenerationSettingsMock);

        $this->codeGeneratorMock->expects($this->once())
            ->method('generate')
            ->with($codeGenerationSettingsMock, null)
            ->willReturn([$poolCode]);

        $poolCodeMock = $this->getMockForAbstractClass(PoolCodeInterface::class);
        $poolCodeMock->expects($this->once())
            ->method('setPoolId')
            ->with($poolData['pool_id'])
            ->willReturnSelf();
        $poolCodeMock->expects($this->once())
            ->method('setCode')
            ->with($poolCode)
            ->willReturnSelf();
        $poolCodeMock->expects($this->once())
            ->method('setUsed')
            ->with(YesNo::NO)
            ->willReturnSelf();
        $this->poolCodeFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($poolCodeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($poolCodeMock);

        $this->assertEquals([$poolCodeMock], $this->object->generateCodesForPool($poolData['pool_id'], $qty));
    }

    /**
     * Testing of importCodesToPool method
     */
    public function testImportCodesToPool()
    {
        $poolId = 1;
        $codesRawData = [
            ['Code', 'Used'],
            ['poolcode', 'No']
        ];

        $poolCodeMock = $this->getMockForAbstractClass(PoolCodeInterface::class);
        $poolCodeMock->expects($this->once())
            ->method('setPoolId')
            ->with($poolId)
            ->willReturnSelf();

        $this->importPoolCodeMock->expects($this->once())
            ->method('process')
            ->with($codesRawData)
            ->willReturn([$poolCodeMock]);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($poolCodeMock);

        $this->assertEquals([$poolCodeMock], $this->object->importCodesToPool($poolId, $codesRawData));
    }

    /**
     * Testing of pullCodeFromPool method
     */
    public function testPullCodeFromPool()
    {
        $poolId = 1;
        $poolCode = 'poolcode';

        $sortOrderMock = $this->getMockBuilder(SortOrder::class)
            ->getMock();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->at(0))
            ->method('addFilter')
            ->with(PoolCodeInterface::USED, false)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->at(1))
            ->method('addFilter')
            ->with(PoolCodeInterface::POOL_ID, $poolId)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setCurrentPage')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->with($sortOrderMock)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $poolCodeMock = $this->getMockForAbstractClass(PoolCodeInterface::class);
        $poolCodeMock->expects($this->once())
            ->method('setUsed')
            ->with(YesNo::YES)
            ->willReturnSelf();
        $poolCodeMock->expects($this->once())
            ->method('getCode')
            ->willReturn($poolCode);
        $poolCodeSearchResultsMock = $this->getMockForAbstractClass(PoolCodeSearchResultsInterface::class);
        $poolCodeSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$poolCodeMock]);
        $this->poolCodeRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($poolCodeSearchResultsMock);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($poolCodeMock);

        $this->assertEquals($poolCode, $this->object->pullCodeFromPool($poolId));
    }
}
