<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Test\Unit\Model;

use Aheadworks\Helpdesk\Model\Serializer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

/**
 * Test for \Aheadworks\Helpdesk\Model\Serializer
 */
class SerializerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Serializer
     */
    private $serializerModel;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->getMockForAbstractClass(ObjectManagerInterface::class);
        $this->objectManagerMock->expects($this->any())
            ->method('get')
            ->with(Serializer::JSON_SERIALIZER_CLASS_NAME)
            ->willReturn(null);

        $this->serializerModel = $objectManager->getObject(
            Serializer::class,
            [
                'objectManager' => $this->objectManagerMock,
            ]
        );
    }

    /**
     * Test serialize method
     */
    public function testSerialize()
    {
        $originalData = 'testSerialize';
        $serializedData = json_encode($originalData);

        $this->assertEquals($serializedData, $this->serializerModel->serialize($originalData));
    }

    /**
     * Test unserialize method
     */
    public function testUnserialize()
    {
        $originalData = 'testSerialize';
        $serializedData = json_encode($originalData);

        $this->assertEquals($originalData, $this->serializerModel->unserialize($serializedData));
    }
}
