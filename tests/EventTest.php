<?php

namespace Jsonpad;

class EventTest extends TestCase {
	
	/**
	* Get the API path for an event
	*/
	public function testGetApiPath() {
		$jsonpad = parent::_getJsonpadInstance();
		$events = $jsonpad->fetchEvents(1);
		$event = $events[0];
		$this->assertSame($event->getApiPath(), "events/" . $event->getId());
	}
	
	/**
	* Load an event, refresh it and make sure it's properties are still intact
	*/
	public function testRefresh() {
		$jsonpad = parent::_getJsonpadInstance();
		$events = $jsonpad->fetchEvents(1);
		$event = $events[0];
		$id = $event->getId();
		$event->refresh();
		$this->assertSame($event->getId(), $id);
	}
}

?>