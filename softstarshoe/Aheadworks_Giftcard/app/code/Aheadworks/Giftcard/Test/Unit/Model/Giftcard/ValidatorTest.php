<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model\Giftcard;

use Aheadworks\Giftcard\Api\Data\GiftcardInterface;
use Aheadworks\Giftcard\Model\Giftcard\Validator;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ValidatorTest
 * Test for \Aheadworks\Giftcard\Model\Giftcard\Validator
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model\Giftcard
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Validator
     */
    private $object;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->object = $objectManager->getObject(
            Validator::class,
            []
        );
    }

    /**
     * Testing of isValid method
     *
     * @param int $state
     * @param bool $expectedValue
     * @dataProvider dataProviderIsValid
     */
    public function testIsValid($state, $expectedValue)
    {
        $giftcardMock = $this->getMockForAbstractClass(GiftcardInterface::class);
        $giftcardMock->expects($this->exactly(3))
            ->method('getState')
            ->willReturn($state);

        $this->assertEquals($expectedValue, $this->object->isValid($giftcardMock));
    }

    /**
     * Retirieve data provider for testIsValid method
     *
     * @return []
     */
    public function dataProviderIsValid()
    {
        return [
            [Status::ACTIVE, true],
            [Status::DEACTIVATED, false],
            [Status::EXPIRED, false],
            [Status::USED, false]
        ];
    }
}
