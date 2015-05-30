<?php

namespace Jsonpad\Resource;

use InvalidArgumentException;

abstract class ResourceBase {
	public static $pageSize = 10;
	public static $resourceName = "";
	public static $resourceCollectionName = "";
	public static $resourcePath = "";
	
	protected $_jsonpad;
	
	/**
	* Get the specified property from an associative array containing the resource's data
	*
	* @param string $propertyName The name of the property to get
	* @param array $data The resource's data
	*
	* @throws InvalidArgumentException if the resource data doesn't contain the requested key
	*
	* @return mixed|null The property value
	*/
	protected static function _getDataProperty($propertyName, $data) {
		if (!array_key_exists($propertyName, $data)) {
			throw new InvalidArgumentException("Resource data is missing key '$propertyName'");
		}
		return $data[$propertyName];
	}
	
	/**
	* Get the API path for this resource
	*
	* @return string The API path for this resource
	*/
	public function getApiPath() {
		return static::$resourcePath;
	}
	
	/**
	* Reload this resource from the jsonpad API
	*/
	public function refresh() {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath()
		);
		return $response[static::$resourceName];
	}
}

?>