<?php

namespace tests\Espo\Core\Utils;

use tests\ReflectionHelper;

class FieldManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected $objects;

    protected $reflection;


    protected function setUp()
    {
        $this->objects['metadata'] = $this->getMockBuilder('\Espo\Core\Utils\Metadata')->disableOriginalConstructor()->getMock();
        $this->objects['language'] = $this->getMockBuilder('\Espo\Core\Utils\Language')->disableOriginalConstructor()->getMock();

        $this->object = new \Espo\Core\Utils\FieldManager($this->objects['metadata'], $this->objects['language']);

        $this->reflection = new ReflectionHelper($this->object);
    }

    protected function tearDown()
    {
        $this->object = NULL;
    }

    public function testCreateExistingField()
    {
        $this->setExpectedException('\Espo\Core\Exceptions\Conflict');

        $data = array(
            "type" => "varchar",
            "maxLength" => "50",
        );

        $this->objects['metadata']
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($data));

        $this->object->create('varName', $data, 'CustomEntity');
    }

    public function testUpdateCoreField()
    {
        //$this->setExpectedException('\Espo\Core\Exceptions\Error');
        $this->objects['metadata']
            ->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));

        $this->objects['language']
            ->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));

        $data = array(
            "type" => "varchar",
            "maxLength" => "50",
            "label" => "Name",
        );

        $this->objects['metadata']
            ->expects($this->exactly(6))
            ->method('get')
            ->will($this->returnValue($data));

        $this->assertTrue($this->object->update('name', $data, 'Account'));
    }

    public function testUpdateCustomFieldIsNotChanged()
    {
        $data = array(
            "type" => "varchar",
            "maxLength" => "50",
            "isCustom" => true,
        );

        $this->objects['metadata']
            ->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValue($data));

        $this->objects['metadata']
            ->expects($this->never())
            ->method('set')
            ->will($this->returnValue(true));

        $this->assertTrue($this->object->update('varName', $data, 'CustomEntity'));
    }

    public function testUpdateCustomField()
    {
        $data = array(
            "type" => "varchar",
            "maxLength" => "50",
            "isCustom" => true,
        );

        $this->objects['metadata']
            ->expects($this->exactly(6))
            ->method('get')
            ->will($this->returnValue($data));

        $this->objects['metadata']
            ->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));

        $data = array(
            "type" => "varchar",
            "maxLength" => "150",
            "required" => true,
            "isCustom" => true,
        );

        $this->assertTrue($this->object->update('varName', $data, 'CustomEntity'));
    }


    public function testRead()
    {
        $data = array(
            "type" => "varchar",
            "maxLength" => "50",
            "isCustom" => true,
            "label" => 'Var Name',
        );

        $this->objects['metadata']
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($data));

        $this->objects['language']
            ->expects($this->once())
            ->method('translate')
            ->will($this->returnValue('Var Name'));

        $this->assertEquals($data, $this->object->read('varName', 'Account'));
    }

    public function testNormalizeDefs()
    {
        $input1 = 'fielName';
        $input2 = array(
            "type" => "varchar",
            "maxLength" => "50",
        );
        $result = array(
            'fields' => array(
                'fielName' => array(
                    "type" => "varchar",
                    "maxLength" => "50",
                ),
            ),
        );
        $this->assertEquals($result, $this->reflection->invokeMethod('normalizeDefs', array($input1, $input2, 'CustomEntity')));
    }

    public function testDeleteTestFile()
    {
        $file = 'custom/Espo/Custom/Resources/metadata/entityDefs/CustomEntity.json';
        if (file_exists($file)) {
            @unlink($file);
        }
    }





}

?>
