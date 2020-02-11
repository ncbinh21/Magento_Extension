<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model\Giftcard;

use Aheadworks\Giftcard\Model\Giftcard\CodeGenerator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Giftcard\Model\ResourceModel\Validator\GiftcardIsUnique;
use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterface;
use Aheadworks\Giftcard\Model\Config;

/**
 * Class CodeGeneratorTest
 * Test for \Aheadworks\Giftcard\Model\Giftcard\CodeGenerator
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model\Giftcard
 */
class CodeGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CodeGenerator
     */
    private $object;

    /**
     * @var GiftcardIsUnique|\PHPUnit_Framework_MockObject_MockObject
     */
    private $giftcardIsUniqueValidatorMock;

    /**
     * @var CodeGenerationSettingsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $codeGenerationSettingsFactoryMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->giftcardIsUniqueValidatorMock = $this->getMockBuilder(GiftcardIsUnique::class)
            ->setMethods(['validate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->codeGenerationSettingsFactoryMock = $this->getMockBuilder(CodeGenerationSettingsInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(
                [
                    'getGiftcardCodeLength',
                    'getGiftcardCodeFormat',
                    'getGiftcardCodePrefix',
                    'getGiftcardCodeSuffix',
                    'getGiftcardCodeDashAtEvery'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = $objectManager->getObject(
            CodeGenerator::class,
            [
                'giftcardIsUniqueValidator' => $this->giftcardIsUniqueValidatorMock,
                'codeGenerationSettingsFactory' => $this->codeGenerationSettingsFactoryMock,
                'config' => $this->configMock,
                'codeParameters' => [
                    'delimiter' => '-',
                    'charset' => [
                        'numeric' => '0123456789'
                    ]
                ]
            ]
        );
    }

    /**
     * Testing of generate method
     */
    public function testGenerate()
    {
        $codeGenerationSettingsData = [
            'qty' => 1,
            'code_length' => 12,
            'code_format' => 'numeric',
            'code_prefix' => null,
            'code_suffix' => null,
            'code_delimiter_at_every' => 2
        ];

        $codeGenerationSettingsMock = $this->getMockForAbstractClass(CodeGenerationSettingsInterface::class);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getQty')
            ->willReturn($codeGenerationSettingsData['qty']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($codeGenerationSettingsData['code_format']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getLength')
            ->willReturn($codeGenerationSettingsData['code_length']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getDelimiterAtEvery')
            ->willReturn($codeGenerationSettingsData['code_delimiter_at_every']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getDelimiter')
            ->willReturn(null);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getPrefix')
            ->willReturn($codeGenerationSettingsData['code_prefix']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getSuffix')
            ->willReturn($codeGenerationSettingsData['code_suffix']);

        $this->giftcardIsUniqueValidatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->assertTrue(is_array($this->object->generate($codeGenerationSettingsMock, null)));
    }

    /**
     * Testing of generate method on exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Unable to create Gift Card code
     */
    public function testGenerateException()
    {
        $codeGenerationSettingsData = [
            'qty' => 1,
            'code_length' => 12,
            'code_format' => 'numeric',
            'code_prefix' => null,
            'code_suffix' => null,
            'code_delimiter_at_every' => 2
        ];

        $codeGenerationSettingsMock = $this->getMockForAbstractClass(CodeGenerationSettingsInterface::class);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getQty')
            ->willReturn($codeGenerationSettingsData['qty']);
        $codeGenerationSettingsMock->expects($this->exactly(1000))
            ->method('getFormat')
            ->willReturn($codeGenerationSettingsData['code_format']);
        $codeGenerationSettingsMock->expects($this->exactly(1000))
            ->method('getLength')
            ->willReturn($codeGenerationSettingsData['code_length']);
        $codeGenerationSettingsMock->expects($this->exactly(1000))
            ->method('getDelimiterAtEvery')
            ->willReturn($codeGenerationSettingsData['code_delimiter_at_every']);
        $codeGenerationSettingsMock->expects($this->exactly(1000))
            ->method('getDelimiter')
            ->willReturn(null);
        $codeGenerationSettingsMock->expects($this->exactly(1000))
            ->method('getPrefix')
            ->willReturn($codeGenerationSettingsData['code_prefix']);
        $codeGenerationSettingsMock->expects($this->exactly(1000))
            ->method('getSuffix')
            ->willReturn($codeGenerationSettingsData['code_suffix']);

        $this->giftcardIsUniqueValidatorMock->expects($this->exactly(1000))
            ->method('validate')
            ->willReturn(false);

        $this->assertTrue(is_array($this->object->generate($codeGenerationSettingsMock, null)));
    }

    /**
     * Testing of generate method from setting config
     */
    public function testGenerateFromSettingConfig()
    {
        $websiteId = 1;
        $codeGenerationSettingsData = [
            'qty' => 1,
            'code_length' => 12,
            'code_format' => 'numeric',
            'code_prefix' => null,
            'code_suffix' => null,
            'code_delimiter_at_every' => 2
        ];

        $codeGenerationSettingsMock = $this->getMockForAbstractClass(CodeGenerationSettingsInterface::class);
        $this->codeGenerationSettingsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($codeGenerationSettingsMock);

        $this->configMock->expects($this->once())
            ->method('getGiftcardCodeLength')
            ->with($websiteId)
            ->willReturn($codeGenerationSettingsData['code_length']);
        $this->configMock->expects($this->once())
            ->method('getGiftcardCodeFormat')
            ->with($websiteId)
            ->willReturn($codeGenerationSettingsData['code_format']);
        $this->configMock->expects($this->once())
            ->method('getGiftcardCodePrefix')
            ->with($websiteId)
            ->willReturn($codeGenerationSettingsData['code_prefix']);
        $this->configMock->expects($this->once())
            ->method('getGiftcardCodeSuffix')
            ->with($websiteId)
            ->willReturn($codeGenerationSettingsData['code_suffix']);
        $this->configMock->expects($this->once())
            ->method('getGiftcardCodeDashAtEvery')
            ->with($websiteId)
            ->willReturn($codeGenerationSettingsData['code_delimiter_at_every']);

        $codeGenerationSettingsMock->expects($this->once())
            ->method('setQty')
            ->with($codeGenerationSettingsData['qty'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setLength')
            ->with($codeGenerationSettingsData['code_length'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setFormat')
            ->with($codeGenerationSettingsData['code_format'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setPrefix')
            ->with($codeGenerationSettingsData['code_prefix'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setSuffix')
            ->with($codeGenerationSettingsData['code_suffix'])
            ->willReturnSelf();
        $codeGenerationSettingsMock->expects($this->once())
            ->method('setDelimiterAtEvery')
            ->with($codeGenerationSettingsData['code_delimiter_at_every'])
            ->willReturnSelf();

        $codeGenerationSettingsMock->expects($this->once())
            ->method('getQty')
            ->willReturn($codeGenerationSettingsData['qty']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($codeGenerationSettingsData['code_format']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getLength')
            ->willReturn($codeGenerationSettingsData['code_length']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getDelimiterAtEvery')
            ->willReturn($codeGenerationSettingsData['code_delimiter_at_every']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getDelimiter')
            ->willReturn(null);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getPrefix')
            ->willReturn($codeGenerationSettingsData['code_prefix']);
        $codeGenerationSettingsMock->expects($this->once())
            ->method('getSuffix')
            ->willReturn($codeGenerationSettingsData['code_suffix']);

        $this->giftcardIsUniqueValidatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->assertTrue(is_array($this->object->generate(null, $websiteId)));
    }
}
