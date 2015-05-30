<?php

namespace Jsonpad;

class TestCase extends \PHPUnit_Framework_TestCase {
	public static $username = "phplibtest";
	public static $apiToken = "D4iycjBNjwMY19POGQ8iEDi5ZqcIiM33";
	private static $jsonpadInstance;
	
	/**
	* Get a jsonpad API connector instance for testing
	*
	* @return \Jsonpad\Jsonpad A jsonpad API connector
	*/
	protected static function _getJsonpadInstance() {
		if (empty(self::$jsonpadInstance)) {
			self::$jsonpadInstance = new Jsonpad(self::$username, self::$apiToken);
		}
		return self::$jsonpadInstance;
	}
	
	/**
	* Get data for a test list
	*
	* @param bool $includeSchema True if the list data should include a schema
	* @param bool $includeIndexes True if the list data should include indexes
	*
	* @return array An array containing 3 elements: a random name for the list, a list schema (or
	*	null) and an array of indexes (or an empty array) that can be used to initialise the list
	*/
	protected static function _createTestListData($includeSchema, $includeIndexes) {
		// List schema
		$schema = array(
				"title" => "Test Schema",
				"type" => "object",
				"properties" => array(
					"test_property_1" => array(
						"type" => "string"
					),
					"test_property_2" => array(
						"type" => "number"
					)
				),
				"required" => array(
					"test_property_1"
				)
			);
		
		// List indexes
		$indexes = array(
				array(
					"name" => "test_property_1",
					"path" => "/test_property_1",
					"type" => "string",
					"default_descending" => true
				)
			);
		
		// Return list data
		return array(
			"name" => self::_randomString(rand(4, 8)),
			"schema" => $includeSchema ? $schema : null,
			"indexes" => $includeIndexes ? $indexes : null
		);
	}
	
	/**
	* Get data for a test item
	*
	* @return array The test item data
	*/
	protected static function _createTestItemData() {
		// Basic data, to ensure that it conforms to the schema returned by _createTestListData()
		$data = array(
			"test_property_1" => self::_randomString(rand(1, 20)),
			"test_property_2" => rand(0, 100)
		);
		
		// Add some random properties to the data
		for ($i = rand(0, 5); $i--;) {
			$data[self::_randomString(rand(1, 20))] = self::_randomDataProperty();
		}
		return $data;
	}
	
	/**
	* Get a random string
	*
	* @param int $length The length of the random string to return
	*
	* @return string The random string
	*/
	private static function _randomString($length) {
		$alpha = "abcdefghijklmnopqrstuvwxyz";
		return substr(str_shuffle($alpha), 0, $length);
	}
	
	/**
	* Get a random data property, either a string, a number, an array of strings, an array of
	* numbers, a random bool (true/false) or null
	*
	* @return mixed The random data property
	*/
	private static function _randomDataProperty() {
		$result = null;
		$types = array("number", "string", "array_number", "array_string", "bool", "null");
		shuffle($types);
		switch ($types[0]) {
			case "number": $result = rand(0, 100); break;
			case "string": $result = self::_randomString(rand(1, 20)); break;
			case "array_number":
				$result = array();
				for ($i = rand(0, 5); $i--;) {
					$result[] = rand(0, 100);
				}
				break;
			case "array_string":
				$result = array();
				for ($i = rand(0, 5); $i--;) {
					$result[] = self::_randomString(rand(1, 20));
				}
				break;
			case "bool": $result = rand(1, 2) == 1; break;
			default: break;
		}
		return $result;
	}
}

?>