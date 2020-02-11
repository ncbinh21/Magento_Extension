<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model\Service;

use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterface as GiftcardQuoteInterface;
use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Model\Service\GiftcardCartService;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\QuoteInterfaceFactory as GiftcardQuoteInterfaceFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote\CollectionFactory as GiftcardQuoteCollectionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote\Collection as GiftcardQuoteCollection;
use Aheadworks\Giftcard\Model\Giftcard\Validator as GiftcardValidator;
use Magento\Quote\Api\Data\CartExtensionInterface;
use Magento\Quote\Model\Quote as QuoteModel;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class GiftcardCartServiceTest
 * Test for \Aheadworks\Giftcard\Model\Service\GiftcardCartService
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model\Service
 */
class GiftcardCartServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GiftcardCartService
     */
    private $object;

    /**
     * @var GiftcardRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardRepositoryMock;

    /**
     * @var CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteRepositoryMock;

    /**
     * @var CartExtensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartExtensionFactoryMock;

    /**
     * @var GiftcardQuoteInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardQuoteFactoryMock;

    /**
     * @var GiftcardQuoteCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardQuoteCollectionFactoryMock;

    /**
     * @var GiftcardValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardValidatorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->giftcardRepositoryMock = $this->getMockForAbstractClass(GiftcardRepositoryInterface::class);
        $this->quoteRepositoryMock = $this->getMockForAbstractClass(CartRepositoryInterface::class);
        $this->cartExtensionFactoryMock = $this->getMockBuilder(CartExtensionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardQuoteFactoryMock = $this->getMockBuilder(GiftcardQuoteInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardQuoteCollectionFactoryMock = $this->getMockBuilder(GiftcardQuoteCollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->giftcardValidatorMock = $this->getMockBuilder(GiftcardValidator::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            GiftcardCartService::class,
            [
                'giftcardRepository' => $this->giftcardRepositoryMock,
                'quoteRepository' => $this->quoteRepositoryMock,
                'cartExtensionFactory' => $this->cartExtensionFactoryMock,
                'giftcardQuoteFactory' => $this->giftcardQuoteFactoryMock,
                'giftcardQuoteCollectionFactory' => $this->giftcardQuoteCollectionFactoryMock,
                'giftcardValidator' => $this->giftcardValidatorMock
            ]
        );
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $giftcardQuoteMock = $this->getMockForAbstractClass(GiftcardQuoteInterface::class);
        $cartId = 1;
        $expectedValue = [$giftcardQuoteMock];

        $quoteMock = $this->getMockBuilder(QuoteModel::class)
            ->setMethods(['getItemsCount', 'getExtensionAttributes'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteMock);

        $quoteExtensionAttributesMock = $this->getMockForAbstractClass(
            CartExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getAwGiftcardCodes']
        );
        $quoteExtensionAttributesMock->expects($this->exactly(2))
            ->method('getAwGiftcardCodes')
            ->willReturn($expectedValue);
        $quoteMock->expects($this->exactly(3))
            ->method('getExtensionAttributes')
            ->willReturn($quoteExtensionAttributesMock);

        $this->assertEquals($expectedValue, $this->object->get($cartId));
    }

    /**
     * Testing of get method
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart 1 doesn't contain products
     */
    public function testGetOnException()
    {
        $cartId = 1;

        $quoteModelMock = $this->getMockBuilder(QuoteModel::class)
            ->setMethods(['getItemsCount'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteModelMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(null);
        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteModelMock);

        $this->object->get($cartId);
    }

    /**
     * Testing of set method
     */
    public function testSet()
    {
        $cartId = 1;
        $giftcardCode = 'gccode';
        $giftcardId = 1;
        $websiteId = 1;
        $expectedValue = true;

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $quoteMock = $this->getMockBuilder(QuoteModel::class)
            ->setMethods(
                [
                    'getItemsCount',
                    'getStore',
                    'getId',
                    'getExtensionAttributes',
                    'getShippingAddress',
                    'collectTotals',
                    'setExtensionAttributes'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $quoteMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $quoteMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($cartId);
        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteMock);

        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($giftcardId);
        $giftcardMock->expects($this->once())
            ->method('getCode')
            ->willReturn($giftcardCode);
        $giftcardMock->expects($this->exactly(2))
            ->method('getBalance')
            ->willReturn(10);
        $this->giftcardRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($giftcardCode, $websiteId)
            ->willReturn($giftcardMock);

        $this->giftcardValidatorMock->expects($this->once())
            ->method('isValid')
            ->with($giftcardMock)
            ->willReturn(true);

        $giftcardQuoteCollectionMock = $this->getMockBuilder(GiftcardQuoteCollection::class)
            ->setMethods(['addFieldToFilter', 'load', 'getItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $giftcardQuoteCollectionMock->expects($this->at(0))
            ->method('addFieldToFilter')
            ->with('quote_id', $cartId)
            ->willReturnSelf();
        $giftcardQuoteCollectionMock->expects($this->at(1))
            ->method('addFieldToFilter')
            ->with('giftcard_id', $giftcardId)
            ->willReturnSelf();
        $giftcardQuoteCollectionMock->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $giftcardQuoteCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $this->giftcardQuoteCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardQuoteCollectionMock);

        $shippingAddressMock = $this->getMockForAbstractClass(
            AddressInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setCollectShippingRates']
        );
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true);
        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $quoteMock->expects($this->at(4))
            ->method('getExtensionAttributes')
            ->willReturn(null);
        $quoteExtensionAttributesMock = $this->getMockForAbstractClass(
            CartExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getAwGiftcardCodes', 'setAwGiftcardCodes']
        );
        $this->cartExtensionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($quoteExtensionAttributesMock);
        $giftcardQuoteMock = $this->getMockForAbstractClass(GiftcardQuoteInterface::class);
        $giftcardQuoteMock->expects($this->once())
            ->method('setGiftcardId')
            ->willReturnSelf();
        $giftcardQuoteMock->expects($this->once())
            ->method('setGiftcardCode')
            ->willReturnSelf();
        $giftcardQuoteMock->expects($this->once())
            ->method('setGiftcardBalance')
            ->willReturnSelf();
        $giftcardQuoteMock->expects($this->once())
            ->method('setQuoteId')
            ->willReturnSelf();
        $giftcardQuoteMock->expects($this->once())
            ->method('setBaseGiftcardAmount')
            ->willReturnSelf();
        $giftcardQuoteMock->expects($this->once())
            ->method('getGiftcardCode')
            ->willReturn($giftcardCode);
        $giftcardQuoteMock->expects($this->once())
            ->method('isRemove')
            ->willReturn(null);
        $this->giftcardQuoteFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($giftcardQuoteMock);
        $quoteExtensionAttributesMock->expects($this->at(0))
            ->method('getAwGiftcardCodes')
            ->willReturn(null);
        $quoteExtensionAttributesMock->expects($this->once())
            ->method('setAwGiftcardCodes')
            ->with([$giftcardQuoteMock]);
        $quoteMock->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($quoteExtensionAttributesMock);

        $quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();
        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($quoteMock)
            ->willReturnSelf();

        $quoteExtensionAttributesMock->expects($this->at(2))
            ->method('getAwGiftcardCodes')
            ->willReturn([$giftcardQuoteMock]);
        $quoteExtensionAttributesMock->expects($this->at(3))
            ->method('getAwGiftcardCodes')
            ->willReturn([$giftcardQuoteMock]);
        $quoteMock->expects($this->at(8))
            ->method('getExtensionAttributes')
            ->willReturn($quoteExtensionAttributesMock);
        $quoteMock->expects($this->at(9))
            ->method('getExtensionAttributes')
            ->willReturn($quoteExtensionAttributesMock);
        $quoteMock->expects($this->at(10))
            ->method('getExtensionAttributes')
            ->willReturn($quoteExtensionAttributesMock);

        $this->assertEquals($expectedValue, $this->object->set($cartId, $giftcardCode));
    }

    /**
     * Testing of remove method
     */
    public function testRemove()
    {
        $cartId = 1;
        $giftcardCode = 'gccode';
        $expectedValue = true;

        $quoteMock = $this->getMockBuilder(QuoteModel::class)
            ->setMethods(['getItemsCount', 'getExtensionAttributes', 'getShippingAddress', 'collectTotals'])
            ->disableOriginalConstructor()
            ->getMock();
        $quoteMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn(2);
        $this->quoteRepositoryMock->expects($this->once())
            ->method('getActive')
            ->with($cartId)
            ->willReturn($quoteMock);

        $shippingAddressMock = $this->getMockForAbstractClass(
            AddressInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['setCollectShippingRates']
        );
        $shippingAddressMock->expects($this->once())
            ->method('setCollectShippingRates')
            ->with(true);
        $quoteMock->expects($this->once())
            ->method('getShippingAddress')
            ->willReturn($shippingAddressMock);

        $quoteExtensionAttributesMock = $this->getMockForAbstractClass(
            CartExtensionInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getAwGiftcardCodes']
        );
        $giftcardQuoteMock = $this->getMockForAbstractClass(GiftcardQuoteInterface::class);
        $giftcardQuoteMock->expects($this->once())
            ->method('getGiftcardCode')
            ->willReturn($giftcardCode);
        $giftcardQuoteMock->expects($this->once())
            ->method('setIsRemove')
            ->with(true);
        $quoteExtensionAttributesMock->expects($this->exactly(2))
            ->method('getAwGiftcardCodes')
            ->willReturn([$giftcardQuoteMock]);
        $quoteMock->expects($this->exactly(3))
            ->method('getExtensionAttributes')
            ->willReturn($quoteExtensionAttributesMock);

        $quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();
        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($quoteMock)
            ->willReturnSelf();

        $this->assertEquals($expectedValue, $this->object->remove($cartId, $giftcardCode));
    }
}
