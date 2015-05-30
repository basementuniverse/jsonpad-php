<?php

namespace Jsonpad;

class JsonpadTest extends TestCase {
	
	/**
	* Create a list
	*/
	public function testCreateList() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		$this->assertSame($list->getName(), $listData["name"]);
		
		// Delete the list
		$list->delete();
	}
	
	/**
	* Create a list, fetch it from the API
	*/
	public function testFetchList() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a list
		$listData = parent::_createTestListData(false, false);
		$list = $jsonpad->createList($listData["name"]);
		
		// Fetch the list
		$listCopy = $jsonpad->fetchList($listData["name"]);
		$this->assertSame($list->getName(), $listCopy->getName());
		
		// Delete the list
		$listCopy->delete();
	}
	
	/**
	* Create multiple lists and fetch a page of lists from the API
	*/
	public function testFetchLists() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Create a couple of lists
		$listCount = 3;
		for ($i = 0; $i < $listCount; $i++) {
			$listData = parent::_createTestListData(false, false);
			$jsonpad->createList($listData["name"]);
		}
		
		// Fetch the lists
		$total = 0;
		$lists = $jsonpad->fetchLists(1, null, null, null, $total);
		$this->assertInternalType("array", $lists);
		$this->assertGreaterThanOrEqual(1, $total);
		$this->assertInstanceOf("\Jsonpad\Resource\ItemList", $lists[0]);
		
		// Delete the lists
		foreach ($lists as $list) {
			$list->delete();
		}
	}
	
	/**
	* Fetch a single event from the API
	*/
	public function testFetchEvent() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Fetch the events
		$total = 0;
		$events = $jsonpad->fetchEvents(1, null, null, $total);
		$this->assertInternalType("array", $events);
		$this->assertGreaterThanOrEqual(1, $total);
		$this->assertInstanceOf("\Jsonpad\Resource\Event", $events[0]);
		
		// Fetch a single event
		$event = $jsonpad->fetchEvent($events[0]->getId());
		$this->assertSame($event->getId(), $events[0]->getId());
	}
	
	/**
	* Fetch all events from the API
	*/
	public function testFetchEvents() {
		$jsonpad = parent::_getJsonpadInstance();
		
		// Fetch the events
		$total = 0;
		$events = $jsonpad->fetchEvents(1, null, null, $total);
		$this->assertInternalType("array", $events);
		$this->assertGreaterThanOrEqual(1, $total);
		$this->assertInstanceOf("\Jsonpad\Resource\Event", $events[0]);
	}
}