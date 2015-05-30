<?php

namespace Jsonpad\Resource;

class ItemListIndex extends ResourceBase {
	public static $resourceName = "index";
	public static $resourceCollectionName = "indexes";
	public static $resourcePath = "indexes";
	
	private $_list;
	private $_name;
	private $_path;
	private $_type;
	private $_defaultDescending;
	
	/**
	* Create a new index instance from the specified index data
	*
	* @param \Jsonpad\Jsonpad $jsonpad The API connector used for loading this resource
	* @param \Jsonpad\Resource\ItemList $list The list that this index belongs to
	* @param array $index The index data
	*/
	public function __construct($jsonpad, $list, $index) {
		$this->_jsonpad = $jsonpad;
		$this->_list = $list;
		$this->_name = parent::_getDataProperty("name", $index);
		$this->_path = parent::_getDataProperty("path", $index);
		$this->_type = parent::_getDataProperty("type", $index);
		$this->_defaultDescending = parent::_getDataProperty("default_descending", $index);
	}
	
	/**
	* Get the API path for this index
	*
	* @return string The API path for this index
	*/
	public function getApiPath() {
		return $this->_list->getApiPath() . "/" . self::$resourcePath . "/" . $this->_name;
	}
	
	/**
	* Get the name for this index
	*
	* @return string The name for this index
	*/
	public function getName() {
		return $this->_name;
	}
	
	/**
	* Get the JSON pointer path for this index
	*
	* @return string The JSON pointer path for this index
	*/
	public function getPath() {
		return $this->_path;
	}
	
	/**
	* Get the type of this index (either "number" or "string")
	*
	* @return string The type for this index
	*/
	public function getType() {
		return $this->_type;
	}
	
	/**
	* Check if this index should sort items in descending order by default
	*
	* @return bool True if this index should sort items in descending order by default
	*/
	public function getDefaultDescending() {
		return $this->_defaultDescending;
	}
	
	/**
	* Reload this index from the API
	*/
	public function refresh() {
		$index = parent::refresh();
		$this->_name = $index["name"];
		$this->_path = $index["path"];
		$this->_type = $index["type"];
		$this->_defaulDescending = $index["default_descending"];
	}
}

?>