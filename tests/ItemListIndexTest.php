<?php

namespace Jsonpad;

class ItemListIndexTest extends TestCase {
	
	/**
	* Get the API path for an index
	*/
	public function testGetApiPath() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list with indexes
		$listData = parent::_createTestListData(false, true);
		$list = $jsonpad->createList($listData["name"], null, $listData["indexes"]);
		
		// Get the first index from the list
		$index = $list->fetchIndex("test_property_1");
		$this->assertSame(
			$index->getApiPath(),
			"lists/{$listData['name']}/indexes/" . $listData["indexes"][0]["name"]
		);
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Load an index, refresh it and make sure it's properties are still intact
	*/
	public function testRefresh() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list with indexes
		$listData = parent::_createTestListData(false, true);
		$list = $jsonpad->createList($listData["name"], null, $listData["indexes"]);
		
		// Get the first index from the list
		$index = $list->fetchIndex("test_property_1");
		$name = $index->getName();
		$index->refresh();
		$this->assertSame($index->getName(), $name);
		
		// Delete the list
		$list->delete();
	}
}

?>