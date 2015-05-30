<?php

namespace Jsonpad\Resource;

class Item extends MutableResourceBase {
	public static $resourceName = "item";
	public static $resourceCollectionName = "items";
	public static $resourcePath = "items";
	
	private $_list;
	private $_id;
	private $_data;
	private $_created;
	private $_updated;
	
	/**
	* Create a new item instance from the specified item data
	*
	* @param \Jsonpad\Jsonpad $jsonpad The API connector containing the credentials to use when
	*	saving changes to this resource
	* @param \Jsonpad\Resource\ItemList $list The list that this item belongs to
	* @param array $item The item data
	*/
	public function __construct($jsonpad, $list, $item) {
		$this->_jsonpad = $jsonpad;
		$this->_list = $list;
		$this->_id = parent::_getDataProperty("id", $item);
		$this->_data = parent::_getDataProperty("data", $item);
		$this->_created = parent::_getDataProperty("created", $item);
		$this->_updated = parent::_getDataProperty("updated", $item);
	}
	
	/**
	* Get the API path for this item
	*
	* @return string The API path for this item
	*/
	public function getApiPath() {
		return $this->_list->getApiPath() . "/" . self::$resourcePath . "/" . $this->_id;
	}
	
	/**
	* Get the id for this item
	*
	* @return string The item's id
	*/
	public function getId() {
		return $this->_id;
	}
	
	/**
	* Get the data for this item
	*
	* @return mixed|null The item's data
	*/
	public function getData() {
		return $this->_data;
	}
	
	/**
	* Set the data for this item
	*
	* @param mixed|null $data The new data for this item
	*/
	public function setData($data) {
		$this->_data = $data;
		$this->_dirty = true;
	}
	
	/**
	* Get the created date/time for this item in ISO 8601 format
	*
	* @return string The item's created date/time
	*/
	public function getCreated() {
		return $this->_created;
	}
	
	/**
	* Get the last updated date/time for this item in ISO 8601 format
	*
	* @return string The item's last updated date/time
	*/
	public function getUpdated() {
		return $this->_updated;
	}
	
	/**
	* Save this item's current state to the API
	*/
	public function save() {
		parent::_save($this->_data);
	}
	
	/**
	* Reload this item from the API
	*/
	public function refresh() {
		$item = parent::refresh();
		$this->_id = $item["id"];
		$this->_data = $item["data"];
		$this->_created = $item["created"];
		$this->_updated = $item["updated"];
	}
	
	/**
	* Delete this item
	*/
	public function delete() {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"DELETE",
			$this->getApiPath()
		);
	}
}

?>