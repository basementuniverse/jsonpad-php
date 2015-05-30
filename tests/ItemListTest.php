<?php

namespace Jsonpad;

class ItemListTest extends TestCase {
	
	/**
	* Get the API path for a list
	*/
	public function testGetApiPath() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		$this->assertSame($list->getApiPath(), "lists/{$listData['name']}");
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Rename a list and save it
	*/
	public function testSave() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Rename the list
		$list->setName("monkey123");
		$list->save();
		
		// Fetch the renamed list
		$listCopy = $jsonpad->fetchList("monkey123");
		$this->assertInstanceOf("\Jsonpad\Resource\ItemList", $listCopy);
		
		// Delete the list
		$listCopy->delete();
	}
	
	/**
	* Rename a list and refresh it (should reset the list name)
	*/
	public function testRefresh() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Rename the list
		$originalListName = $list->getName();
		$newListName = "monkey123";
		$list->setName($newListName);
		$this->assertSame($list->getName(), $newListName);
		
		// At this point, the list API path should still be using the original list name
		$this->assertSame($list->getApiPath(), "lists/$originalListName");
		
		// Refresh the list
		$list->refresh();
		$this->assertSame($list->getName(), $originalListName);
		
		// Delete the list
		$list->delete();
	}
}

?>