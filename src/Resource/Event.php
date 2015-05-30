<?php

namespace Jsonpad\Resource;

class Event extends ResourceBase {
	public static $resourceName = "event";
	public static $resourceCollectionName = "events";
	public static $resourcePath = "events";
	
	private $_id;
	private $_time;
	private $_objectType;
	private $_objectId;
	private $_action;
	private $_data;
	
	/**
	* Create a new event instance from the specified event data
	*
	* @param \Jsonpad\Jsonpad $jsonpad The API connector used for loading this resource
	* @param array $event The event data
	*/
	public function __construct($jsonpad, $event) {
		$this->_jsonpad = $jsonpad;
		$this->_id = parent::_getDataProperty("id", $event);
		$this->_time = parent::_getDataProperty("time", $event);
		$this->_objectType = parent::_getDataProperty("object_type", $event);
		$this->_objectId = parent::_getDataProperty("object_id", $event);
		$this->_action = parent::_getDataProperty("action", $event);
		$this->_data = parent::_getDataProperty("data", $event);
	}
	
	/**
	* Get the API path for this event
	*
	* @return string The API path for this event
	*/
	public function getApiPath() {
		return self::$resourcePath . "/" . $this->_id;
	}
	
	/**
	* Get the id for this event
	*
	* @return string The event's id
	*/
	public function getId() {
		return $this->_id;
	}
	
	/**
	* Get the date/time for this event in ISO 8601 format
	*
	* @return string The event's date/time
	*/
	public function getTime() {
		return $this->_time;
	}
	
	/**
	* Get the type of the object associated with this event
	*
	* @return string The event's object type
	*/
	public function getObjectType() {
		return $this->_objectType;
	}
	
	/**
	* Get the id of the object associated with this event
	*
	* @return string The event's object id
	*/
	public function getObjectId() {
		return $this->_objectId;
	}
	
	/**
	* Get the action performed in this event
	*
	* @return string The event's action
	*/
	public function getAction() {
		return $this->_action;
	}
	
	/**
	* Get the data for this event
	*
	* @return mixed|null The event's data, if any data exists for this event
	*/
	public function getData() {
		return $this->_data;
	}
	
	/**
	* Reload this event from the API
	*/
	public function refresh() {
		$event = parent::refresh();
		$this->_id = $event["id"];
		$this->_time = $event["time"];
		$this->_objectType = $event["object_type"];
		$this->_objectId = $event["object_id"];
		$this->_action = $event["action"];
		$this->_data = $event["data"];
	}
}

?>