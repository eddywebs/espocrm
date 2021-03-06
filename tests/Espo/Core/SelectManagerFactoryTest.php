<?php

namespace tests\Espo\Core;


class SelectManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $selectManager;
    
    protected function setUp()
    {
        $entityManager = $this->getMockBuilder('\\Espo\\Core\\ORM\\EntityManager')->disableOriginalConstructor()->getMock();
        $user = $this->getMockBuilder('\\Espo\\Entities\\User')->disableOriginalConstructor()->getMock();
        $acl = $this->getMockBuilder('\\Espo\\Core\\Acl')->disableOriginalConstructor()->getMock();
        $metadata = $this->getMockBuilder('\\Espo\\Core\\Utils\\Metadata')->disableOriginalConstructor()->getMock();
        
        $this->selectManager = new \Espo\Core\SelectManagerFactory($entityManager, $user, $acl, $metadata);
    }
    
    protected function tearDown()
    {
        unset($this->selectManager);
    }
    
    public function testWhere()
    {
        /*$params = array(
            'where' => array(
                array(
                    'type' => 'or',
                    'value' => array(
                        array(
                            'type' => 'like',
                            'field' => 'name',
                            'value' => 'Brom',
                        ),
                        array(
                            'type' => 'like',
                            'field' => 'city',
                            'value' => 'Brom',
                        ),
                    ),
                ),
            )
        );
        
        $result = $this->selectManager->getSelectParams($params);

        $this->assertEquals($result['whereClause'][0]['OR']['name*'], 'Brom');*/
    }
}

