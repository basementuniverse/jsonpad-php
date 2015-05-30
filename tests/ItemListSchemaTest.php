<?php

namespace Jsonpad;

class ItemListSchemaTest extends TestCase {
	
	/**
	* Get the API path for a list schema
	*/
	public function testGetApiPath() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list with a schema
		$listData = parent::_createTestListData(true, false);
		$list = $jsonpad->createList($listData["name"], $listData["schema"]);
		
		// Get the schema from the list
		$schema = $list->fetchSchema();
		$this->assertSame($schema->getApiPath(), "lists/{$listData['name']}/schema");
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Load a schema, refresh it and make sure it's properties are still intact
	*/
	public function testRefresh() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list with a schema
		$listData = parent::_createTestListData(true, false);
		$list = $jsonpad->createList($listData["name"], $listData["schema"]);
		
		// Get the schema from the list
		$schema = $list->fetchSchema();
		$title = $schema->getSchema()["title"];
		$schema->refresh();
		$this->assertSame($schema->getSchema()["title"], $title);
		
		// Delete the list
		$list->delete();
	}
}

?>