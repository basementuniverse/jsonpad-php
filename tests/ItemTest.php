<?php

namespace Jsonpad;

class ItemTest extends TestCase {
	
	/**
	* Get the API path for an item
	*/
	public function testGetApiPath() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Create an item
		$itemData = parent::_createTestItemData();
		$item = $list->createItem($itemData);
		$this->assertSame($item->getApiPath(), "lists/{$listData['name']}/items/" . $item->getId());
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Modify an item's data and save it
	*/
	public function testSave() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Create an item
		$itemData = parent::_createTestItemData();
		$item = $list->createItem($itemData);
		
		// Set the item's data
		$itemData = $item->getData();
		$newTestProperty1Value = "abc";
		$itemData["test_property_1"] = $newTestProperty1Value;
		$item->setData($itemData);
		$item->save();
		
		// Fetch the modified item
		$itemCopy = $list->fetchItem($item->getId());
		$this->assertSame($itemCopy->getData()["test_property_1"], $newTestProperty1Value);
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Modify an item's data and refresh the item (resetting the data)
	*/
	public function testRefresh() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Create an item
		$itemData = parent::_createTestItemData();
		$item = $list->createItem($itemData);
		
		// Set the item's data
		$itemData = $item->getData();
		$originalTestProperty1Value = $itemData["test_property_1"];
		$itemData["test_property_1"] = "abc";
		$item->setData($itemData);
		$item->refresh();
		
		// The item's data should be reset to it's original state
		$this->assertSame($item->getData()["test_property_1"], $originalTestProperty1Value);
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Create an item then delete it
	*/
	public function testDelete() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Create an item
		$itemData = parent::_createTestItemData();
		$item = $list->createItem($itemData);
		
		// Refresh the list and count items
		$list->refresh();
		$this->assertSame($list->getItemCount(), 1);
		
		// Delete the item, refresh the list and count items
		$item->delete();
		$list->refresh();
		$this->assertSame($list->getItemCount(), 0);
		
		// Delete the list
		$list->delete();
	}
}

?>