<?php

namespace Jsonpad\Resource;

class ItemListSchema extends ResourceBase {
	public static $resourceName = "schema";
	public static $resourcePath = "schema";
	
	private $_list;
	private $_schema;
	
	/**
	* Create a new schema instance from the specified schema data
	*
	* @param \Jsonpad\Jsonpad $jsonpad The API connector used for loading this resource
	* @param \Jsonpad\Resource\ItemList $list The list that this schema belongs to
	* @param array $schema The schema data
	*/
	public function __construct($jsonpad, $list, $schema) {
		$this->_jsonpad = $jsonpad;
		$this->_list = $list;
		$this->_schema = $schema;
	}
	
	/**
	* Get the API path for this schema
	*
	* @return string The API path for this schema
	*/
	public function getApiPath() {
		return $this->_list->getApiPath() . "/" . self::$resourcePath;
	}
	
	/**
	* Get the schema data
	*
	* @return string The schema data
	*/
	public function getSchema() {
		return $this->_schema;
	}
	
	/**
	* Reload this schema from the API
	*/
	public function refresh() {
		$schema = parent::refresh();
		$this->_schema = $schema;
	}
}

?>