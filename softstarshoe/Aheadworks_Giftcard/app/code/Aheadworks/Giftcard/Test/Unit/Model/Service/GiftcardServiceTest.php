<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model\Service;

use Aheadworks\Giftcard\Api\Data\GiftcardSearchResultsInterface;
use Aheadworks\Giftcard\Model\Service\GiftcardService;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Giftcard\CodeGenerator;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Giftcard\Model\Giftcard\Validator as GiftcardValidator;
use Aheadworks\Giftcard\Model\Email\Sender;
use Aheadworks\Giftcard\Model\Giftcard\Grouping as GiftcardGrouping;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Aheadworks\Giftcard\Model\Source\History\Comment\Action as SourceHistoryCommentAction;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Aheadworks\Giftcard\Model\Source\EmailStatus;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;
use Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Giftcard\Model\Import\GiftcardCode as ImportGiftcardCode;

/**
 * Class GiftcardServiceTest
 * Test for \Aheadworks\Giftcard\Model\Service\GiftcardService
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model\Service
 */
class GiftcardServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GiftcardService
     */
    private $object;

    /**
     * @var GiftcardRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var PriceCurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceCurrencyMock;

    /**
     * @var CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSessionMock;

    /**
     * @var GiftcardValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardValidatorMock;

    /**
     * @var Sender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $senderMock;

    /**
     * @var GiftcardGrouping|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardGroupingMock;

    /**
     * @var HistoryActionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $historyActionFactoryMock;

    /**
     * @var HistoryEntityInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $historyEntityFactoryMock;

    /**
     * @var EmailStatus|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sourceEmailStatusMock;

    /**
     * @var OrderManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderManagementMock;

    /**
     * OrderStatusHistoryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderStatusHistoryFactoryMock;

    /**
     * OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepositoryMock;

    /**
     * CodeGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeGeneratorMock;

    /**
     * ImportGiftcardCode|\PHPUnit_Framework_MockObject_MockObject
     */
    private $importGiftcardCodeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->giftcardRepositoryMock = $this->getMockForAbstractClass(GiftcardRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->priceCurrencyMock = $this->getMockForAbstractClass(PriceCurrencyInterface::class);
        $this->customerSessionMock = $this->getMockBuilder(CustomerSession::class)
            ->setMethods(['getCustomer'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardValidatorMock = $this->getMockBuilder(GiftcardValidator::class)
            ->setMethods(['isValid'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->senderMock = $this->getMockBuilder(Sender::class)
            ->setMethods(['sendGiftcards'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardGroupingMock = $this->getMockBuilder(GiftcardGrouping::class)
            ->setMethods(['process'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->historyActionFactoryMock = $this->getMockBuilder(HistoryActionInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->historyEntityFactoryMock = $this->getMockBuilder(HistoryEntityInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->sourceEmailStatusMock = $this->getMockBuilder(EmailStatus::class)
            ->setMethods(['getOptionByValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderManagementMock = $this->getMockForAbstractClass(OrderManagementInterface::class);
        $this->orderStatusHistoryFactoryMock = $this->getMockBuilder(OrderStatusHistoryInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderRepositoryMock = $this->getMockForAbstractClass(OrderRepositoryInterface::class);
        $this->codeGeneratorMock = $this->getMockBuilder(CodeGenerator::class)
            ->setMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->importGiftcardCodeMock = $this->getMockBuilder(ImportGiftcardCode::class)
            ->setMethods(['process'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            GiftcardService::class,
            [
                'giftcardRepository' => $this->giftcardRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'priceCurrency' => $this->priceCurrencyMock,
                'customerSession' => $this->customerSessionMock,
                'giftcardValidator' => $this->giftcardValidatorMock,
                'sender' => $this->senderMock,
                'giftcardGrouping' => $this->giftcardGroupingMock,
                'historyActionFactory' => $this->historyActionFactoryMock,
                'historyEntityFactory' => $this->historyEntityFactoryMock,
                'sourceEmailStatus' => $this->sourceEmailStatusMock,
                'orderManagement' => $this->orderManagementMock,
                'orderStatusHistoryFactory' => $this->orderStatusHistoryFactoryMock,
                'orderRepository' => $this->orderRepositoryMock,
                'codeGenerator' => $this->codeGeneratorMock,
                'importGiftcardCode' => $this->importGiftcardCodeMock
            ]
        );
    }

    /**
     * Testing of sendGiftcardByCode method
     */
    public function testSendGiftcardByCode()
    {
        $giftcardCode = 'gccode';
        $giftcardEmailTemplate = 'template';
        $giftcardDeliveryDate = null;
        $sendStatus = EmailStatus::SENT;
        $sendStatusText = 'Sent';

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->once())
            ->method('getEmailTemplate')
            ->willReturn($giftcardEmailTemplate);
        $giftcardMock->expects($this->once())
            ->method('getDeliveryDate')
            ->willReturn($giftcardDeliveryDate);
        $this->giftcardRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->willReturn($giftcardMock);
        $this->giftcardValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($giftcardMock)
            ->willReturn(true);

        $this->giftcardGroupingMock->expects($this->once())
            ->method('process')
            ->with([$giftcardMock])
            ->willReturn([[$giftcardMock]]);
        $this->senderMock->expects($this->once())
            ->method('sendGiftcards')
            ->with([$giftcardMock])
            ->willReturn($sendStatus);
        $this->sourceEmailStatusMock->expects($this->once())
            ->method('getOptionByValue')
            ->with($sendStatus)
            ->willReturn($sendStatusText);

        $historyEntityMock = $this->getMockForAbstractClass(HistoryEntityInterface::class);
        $historyEntityMock->expects($this->once())
            ->method('setEntityType')
            ->with(SourceHistoryEntityType::EMAIL_STATUS)
            ->willReturnSelf();
        $historyEntityMock->expects($this->once())
            ->method('setEntityId')
            ->with($sendStatus)
            ->willReturnSelf();
        $historyEntityMock->expects($this->once())
            ->method('setEntityLabel')
            ->with($sendStatusText)
            ->willReturnSelf();
        $this->historyEntityFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($historyEntityMock);

        $historyActionMock = $this->getMockForAbstractClass(HistoryActionInterface::class);
        $historyActionMock->expects($this->once())
            ->method('setActionType')
            ->with(SourceHistoryCommentAction::DELIVERY_DATE_EMAIL_STATUS)
            ->willReturnSelf();
        $historyActionMock->expects($this->once())
            ->method('setEntities')
            ->with([$historyEntityMock])
            ->willReturnSelf();
        $this->historyActionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($historyActionMock);

        $giftcardMock->expects($this->once())
            ->method('setCurrentHistoryAction')
            ->with($historyActionMock)
            ->willReturnSelf();
        $giftcardMock->expects($this->once())
            ->method('setEmailSent')
            ->with($sendStatus)
            ->willReturnSelf();

        $this->giftcardRepositoryMock->expects($this->once())
            ->method('save')
            ->with($giftcardMock)
            ->willReturnSelf();

        $giftcardMock->expects($this->once())
            ->method('getOrderId')
            ->willReturn(null);

        $this->assertEquals([$giftcardMock], $this->object->sendGiftcardByCode($giftcardCode));
    }

    /**
     * Testing of getCustomerGiftcards method
     */
    public function testGetCustomerGiftcards()
    {
        $cartId = 1;
        $storeId = 1;
        $customerEmail = 'email@gmail.com';
        $giftcardBalance = 10;

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock->expects($this->once())
            ->method('getEmail')
            ->willReturn($customerEmail);
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->at(0))
            ->method('addFilter')
            ->with('quote', $cartId)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->at(1))
            ->method('addFilter')
            ->with(GiftcardInterface::RECIPIENT_EMAIL, $customerEmail)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->at(2))
            ->method('addFilter')
            ->with(GiftcardInterface::STATE, Status::ACTIVE)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->at(3))
            ->method('addFilter')
            ->with(GiftcardInterface::EMAIL_SENT, [EmailStatus::SENT, EmailStatus::NOT_SEND], 'in')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->once())
            ->method('getBalance')
            ->willReturn($giftcardBalance);
        $giftcardMock->expects($this->once())
            ->method('setBalance')
            ->with($giftcardBalance);
        $giftcardSearchResultsMock = $this->getMockForAbstractClass(GiftcardSearchResultsInterface::class);
        $giftcardSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$giftcardMock]);
        $this->giftcardRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($giftcardSearchResultsMock);

        $this->priceCurrencyMock->expects($this->once())
            ->method('convert')
            ->with($giftcardBalance, $storeId)
            ->willReturn($giftcardBalance);

        $this->assertEquals([$giftcardMock], $this->object->getCustomerGiftcards(null, $cartId, $storeId));
    }

    /**
     * Testing of addCommentToGiftcardOrder method
     */
    public function testAddCommentToGiftcardOrder()
    {
        $orderId = 1;
        $orderStatus = 'status';
        $comment = 'comment';
        $expectedValue = true;

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->once())
            ->method('getOrderId')
            ->willReturn($orderId);

        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);
        $orderMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($orderStatus);
        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->willReturn($orderMock);

        $orderStatusHistoryMock = $this->getMockForAbstractClass(OrderStatusHistoryInterface::class);
        $orderStatusHistoryMock->expects($this->once())
            ->method('setComment')
            ->with($comment)
            ->willReturnSelf();
        $orderStatusHistoryMock->expects($this->once())
            ->method('setIsVisibleOnFront')
            ->with(0)
            ->willReturnSelf();
        $orderStatusHistoryMock->expects($this->once())
            ->method('setIsCustomerNotified')
            ->with(0)
            ->willReturnSelf();
        $orderStatusHistoryMock->expects($this->once())
            ->method('setStatus')
            ->with($orderStatus)
            ->willReturnSelf();
        $orderStatusHistoryMock->expects($this->once())
            ->method('setEntityName')
            ->with('order')
            ->willReturnSelf();

        $this->orderStatusHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderStatusHistoryMock);

        $this->orderManagementMock->expects($this->once())
            ->method('addComment')
            ->with($orderId, $orderStatusHistoryMock)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->addCommentToGiftcardOrder($giftcardMock, $comment));
    }

    /**
     * Testing of generateCodes method
     */
    public function testGenerateCodes()
    {
        $websiteId = 1;
        $giftcardCode = 'gccode';

        $this->codeGeneratorMock->expects($this->once())
            ->method('generate')
            ->with(null, $websiteId)
            ->willReturn([$giftcardCode]);

        $this->assertEquals([$giftcardCode], $this->object->generateCodes($websiteId));
    }

    /**
     * Testing of importCodes method
     */
    public function testImportCodes()
    {
        $codesRawData = [
            ['Type', 'Code', 'Sender Name', 'Sender Email', 'Recipient Name', 'Recipient Email', 'Website'],
            ['Virtual', 'poolcode', 'name', 'email@gmail.com', 'name', 'email@gmail.com', 'Main Website']
        ];

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $this->importGiftcardCodeMock->expects($this->once())
            ->method('process')
            ->with($codesRawData)
            ->willReturn([$giftcardMock]);

        $this->giftcardRepositoryMock->expects($this->once())
            ->method('save')
            ->with($giftcardMock);

        $this->assertEquals([$giftcardMock], $this->object->importCodes($codesRawData));
    }
}
