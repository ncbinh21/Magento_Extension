<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Test\Unit\Model;

use Aheadworks\Giftcard\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigTest
 * Test for \Aheadworks\Giftcard\Model\Config
 *
 * @package Aheadworks\Giftcard\Test\Unit\Model
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Config
     */
    private $object;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->object = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
            ]
        );
    }

    /**
     * Testing of getGiftcardExpireDays method
     */
    public function testGetGiftcardExpireDays()
    {
        $websiteId = 1;
        $expectedValue = 5;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GIFTCARD_EXPIRE_DAYS, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getGiftcardExpireDays($websiteId));
    }

    /**
     * Testing of getEmailSender method
     */
    public function testGetEmailSender()
    {
        $storeId = 1;
        $expectedValue = 'email_sender';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getEmailSender($storeId));
    }

    /**
     * Testing of getEmailSenderName method
     */
    public function testGetEmailSenderName()
    {
        $storeId = 1;
        $sender = 'email_sender';
        $expectedValue = 'email_sender_name';

        $this->scopeConfigMock->expects($this->at(0))
            ->method('getValue')
            ->with(Config::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($sender);

        $this->scopeConfigMock->expects($this->at(1))
            ->method('getValue')
            ->with('trans_email/ident_' . $sender . '/name', ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getEmailSenderName($storeId));
    }

    /**
     * Testing of getGiftcardCodeLength method
     */
    public function testGetGiftcardCodeLength()
    {
        $websiteId = 1;
        $expectedValue = 12;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GIFTCARD_CODE_LENGTH, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getGiftcardCodeLength($websiteId));
    }

    /**
     * Testing of getGiftcardCodePrefix method
     */
    public function testGetGiftcardCodePrefix()
    {
        $websiteId = 1;
        $expectedValue = 'aw';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GIFTCARD_CODE_PREFIX, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getGiftcardCodePrefix($websiteId));
    }

    /**
     * Testing of getGiftcardCodeSuffix method
     */
    public function testGetGiftcardCodeSuffix()
    {
        $websiteId = 1;
        $expectedValue = 'aw';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GIFTCARD_CODE_SUFFIX, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getGiftcardCodeSuffix($websiteId));
    }

    /**
     * Testing of getGiftcardCodeDashAtEvery method
     */
    public function testGetGiftcardCodeDashAtEvery()
    {
        $websiteId = 1;
        $expectedValue = 2;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GIFTCARD_CODE_DASH_EVERY_X_CHARACTERS, ScopeInterface::SCOPE_WEBSITE, $websiteId)
            ->willReturn($expectedValue);

        $this->assertEquals($expectedValue, $this->object->getGiftcardCodeDashAtEvery($websiteId));
    }
}
