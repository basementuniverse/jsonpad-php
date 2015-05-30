<?php

namespace Jsonpad\Resource;

abstract class MutableResourceBase extends ResourceBase {
	protected $_dirty = false;
	
	/**
	* Save this resource back to the jsonpad API
	*
	* @param array|null The data to send to the API containing this resource's new values
	*/
	protected function _save($data) {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"PUT",
			$this->getApiPath(),
			null,
			$data
		);
		$this->_dirty = false;
	}
	
	/**
	* Save this resource back to the jsonpad API
	*/
	public function save() { }
	
	/**
	* Reload this resource from the jsonpad API
	*/
	public function refresh() {
		$this->_dirty = false;
		return parent::refresh();
	}
	
	/**
	* Get a page of events for this resource
	*
	* @param int $page The page of events to return
	* @param int|null $pageSize The number of events per page, if this isn't specified then the
	*	default page size will be used instead
	* @param bool|null $descending True if the events should be sorted in descending order. If this
	*	is null, the default ordering mode for events will be used
	* @param int &$total Outputs the total number of events
	*
	* @return \Jsonpad\Resource\Event[] A page of events for this resource
	*/
	public function fetchEvents(
		$page,
		$pageSize = null,
		$descending = null,
		&$total = null
	) {
		$parameters = array();
		
		// Set the default page size if it isn't specified
		if (!isset($pageSize)) {
			$pageSize = ResourceBase::$pageSize;
		}
		
		// If descending is set (ie. not null), set the descending parameter
		if ($descending !== null) {
			$parameters["descending"] = $descending === true ? "1" : "0";
		}
		
		// Set the page and pagesize parameters
		$parameters["page"] = $page;
		$parameters["page_size"] = $pageSize;
		
		// Request the events from the API
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath() . "/" . Event::$resourcePath,
			$parameters
		);
		
		// Output the total number of events
		if (isset($total)) {
			$total = $response["total"];
		}
		
		// Create event instances for each returned event
		$events = array();
		foreach ($response[Event::$resourceCollectionName] as $event) {
			$events[] = new Event($this, $event);
		}
		return $events;
	}
}

?>