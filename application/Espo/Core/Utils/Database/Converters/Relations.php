<?php

namespace Espo\Core\Utils\Database\Converters;

use Espo\Core\Utils\Util,
	Espo\ORM\Entity;


class Relations
{
	private $metadata;

	public function __construct(\Espo\Core\Utils\Metadata $metadata)
	{
    	$this->metadata = $metadata;
	}

	protected function getMetadata()
	{
		return $this->metadata;
	}


	protected function getSortEntities($entity1, $entity2)
	{
		$entities = array(
        	Util::toCamelCase(lcfirst($entity1)),
        	Util::toCamelCase(lcfirst($entity2)),
		);

		sort($entities);

		return $entities;
	}


	protected function getJoinTable($tableName1, $tableName2)
	{
		$tables = $this->getSortEntities($tableName1, $tableName2);

		return Util::toCamelCase( implode('-', $tables) );
	}


	protected function getForeignField($name, $entityName)
	{
		$foreignField = $this->getMetadata()->get('entityDefs.'.$entityName.'.fields.'.$name);

		if ($foreignField['type'] != Entity::VARCHAR) {
        	$fieldDefs = $this->getMetadata()->get('fields.'.$foreignField['type']);
            $naming = isset($fieldDefs['naming']) ? $fieldDefs['naming'] : 'postfix';

			if (isset($fieldDefs['actualFields']) && is_array($fieldDefs['actualFields'])) {
            	$foreignFieldArray = array();
				foreach($fieldDefs['actualFields'] as $fieldName) {
					if ($fieldName != 'salutation') {
                    	$foreignFieldArray[] = Util::getNaming($name, $fieldName, $naming);
					}
				}
				return explode('|', implode('| |', $foreignFieldArray)); //add an empty string between items
			}
		}

		return $name;
	}


	//todo sedine in foreign fieldDefs a key for current
	public function manyMany($params, $foreignParams)
	{
		return array(
			$params['entityName'] => array(
				'relations' => array(
					$params['link']['name'] => array(
						'type' => Entity::MANY_MANY,
						'entity' => $params['targetEntity'],
						'relationName' => $this->getJoinTable($params['entityName'], $foreignParams['entityName']),
						'key' => 'id', //todo specify 'key'
						'foreignKey' => 'id', //todo specify 'foreignKey'
						'midKeys' => array(
							lcfirst($params['entityName']).'Id',
							lcfirst($foreignParams['entityName']).'Id',
						),
					),
				),
			),
		); 
	}


	public function hasMany($params, $foreignParams)
	{
		$relation = array(
			$params['entityName'] => array (
				'relations' => array(
                	$params['link']['name'] => array(
						'type' => Entity::HAS_MANY,
						'entity' => $params['targetEntity'],
						'foreignKey' => lcfirst($foreignParams['link']['name'].'Id'), //???: 'foreignKey' => $params['link']['name'].'Id',
					),
				),
			),
		);

        return $relation;
	}

	public function belongsTo($params, $foreignParams)
	{
		 $relation = array (
			$params['entityName'] => array (
				'fields' => array(
					$params['link']['name'].'Name' => array(
						'type' => Entity::FOREIGN,
						'relation' => $params['link']['name'],
						//'notStorable' => true,
						'foreign' => $this->getForeignField('name', $foreignParams['entityName']),
					),
					$params['link']['name'].'Id' => array(
						'type' => Entity::FOREIGN_ID,
					),
				),
				'relations' => array(
                	$params['link']['name'] => array(
						'type' => Entity::BELONGS_TO,
						'entity' => $params['targetEntity'],
						'key' => $params['link']['name'].'Id',
						'foreignKey' => 'id', //????
					),
				),
			),
		);

		return $relation;
	}

	public function hasChildren($params, $foreignParams)
	{
		$relation = array(
			$params['entityName'] => array (
				'relations' => array(
                	$params['link']['name'] => array(
						'type' => Entity::HAS_CHILDREN,
						'entity' => $params['targetEntity'],
						'foreignKey' => $foreignParams['link']['name'].'Id', //???: 'foreignKey' => $params['link']['name'].'Id',
						'foreignType' => $foreignParams['link']['name'].'Type', //???: 'foreignKey' => $params['link']['name'].'Id',
					),
				),
			),
		);


		return $relation;
	}

	public function linkParent($params, $foreignParams)
	{
        $relation = array();

		$entities = isset($params['link']['params']['entities']) ? $params['link']['params']['entities'] : array($params['entityName']);

		foreach($entities as $entity) {
        	$relation[$entity] = array (
            	'fields' => array(
                	$params['link']['name'].'Id' => array(
						'type' => Entity::FOREIGN_ID,
					),
					$params['link']['name'].'Type' => array(
						'type' => Entity::FOREIGN_TYPE,
					),
					$params['link']['name'].'Name' => array(
						'type' => Entity::VARCHAR,
						'notStorable' => true,
					),
				),
			);
		}

		return $relation;
	}


	public function linkMultiple($params, $foreignParams)
	{
       	return array(
			$params['entityName'] => array (
	           	'fields' => array(
	               	$params['link']['name'].'Ids' => array(
						'type' => Entity::VARCHAR,
						'notStorable' => true,
					),
					$params['link']['name'].'Names' => array(
						'type' => Entity::VARCHAR,
						'notStorable' => true,
					),
				),
			),
		);
	}


	public function typePersonName($params, $foreignParams)
	{
		$foreignField = $this->getForeignField($params['link']['name'], $params['entityName']);
		$tableName = Util::toUnderScore($params['entityName']);

		$fullList = array(); //contains empty string (" ") like delimiter
		$fieldList = array(); //doesn't contain empty string (" ") like delimiter
		$like = array();
		foreach($foreignField as $fieldName) {

            $fieldNameTrimmed = trim($fieldName);
			if (!empty($fieldNameTrimmed)) {
				$columnName = $tableName.'.'.Util::toUnderScore($fieldNameTrimmed);

            	$fullList[] = $fieldList[] = $columnName;
				$like[] = $columnName." LIKE '{text}'";
			} else {
            	$fullList[] = "'".$fieldName."'";
			}
		}

       	return array(
			$params['entityName'] => array (
	           	'fields' => array(
	               	$params['link']['name'] => array(
						'select' => "TRIM(CONCAT(".implode(", ", $fullList)."))",
					    'where' => array(
					    	'LIKE' => "(".implode(" OR ", $like)." OR CONCAT(".implode(", ", $fullList).") LIKE '{text}')",
					    ),
					    'orderBy' => implode(", ", $fieldList),
					),
				),
			),
		);
	}



	//public function teamRelation($params, $foreignParams)
	public function hasManyWithName($params, $foreignParams)
	{
    	$relationKeys = explode('-', Util::fromCamelCase($params['link']['params']['relationName']));
        $midKeys = array();
		foreach($relationKeys as $key) {
			/*if (strtolower($params['entityName']) == strtolower($key) || $key=='entity') {
            	$midKeys[0] = $key.'Id6666';
			} else {
            	$midKeys[] = $key.'Id';
			}*/
            $midKeys[] = $key.'Id';
		}

		return array(
			$params['entityName'] => array(
				'relations' => array(
					$params['link']['name'] => array(
						'type' => Entity::MANY_MANY,
						'entity' => $params['targetEntity'],
						'relationName' => lcfirst($params['link']['params']['relationName']),
						'midKeys' => $midKeys,
						'conditions' => array('entityType' => $params['entityName']),
					),
				),
			),
		);

	}


	public function hasOne($params, $foreignParams)
	{

	}


}