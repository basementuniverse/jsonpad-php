<?php

namespace Jsonpad;

use InvalidArgumentException;

class Jsonpad {
	private $_username;
	private $_apiToken;
	
	public $listCache;
	public $itemCache;
	
	/**
	* Create a new jsonpad API connector under the context of the specified user credentials
	*
	* @param string $username The account username to use
	* @param string $apiToken The API token to use
	*/
	public function __construct($username, $apiToken) {
		$this->_username = $username;
		$this->_apiToken = $apiToken;
		
		// Create caches
		$this->listCache = new ListCache();
		$this->itemCache = new ItemCache();
	}
	
	/**
	* Get the username for this connector
	*
	* @return string The username associated with this connector
	*/
	public function getUsername() {
		return $this->_username;
	}
	
	/**
	* Set the username for this connector
	*
	* @param string $username The new username to associate with this connector
	*/
	public function setUsername($username) {
		if (is_string($username)) {
			$this->_username = (string)$username;
		}
	}
	
	/**
	* Get the API token for this connector
	*
	* @return string The API token associated with this connector
	*/
	public function getApiToken() {
		return $this->_apiToken;
	}
	
	/**
	* Set the API token for this connector
	*
	* @param string $apiToken The new API token to associate with this connector
	*/
	public function setApiToken($apiToken) {
		if (is_string($apiToken)) {
			$this->apiToken = (string)$apiToken;
		}
	}
	
	/**
	* Create a new list
	*
	* @param string $name The list's name
	* @param bool $realtimeEnabled True if realtime events should be enabled for this list
	* @param array|null $schema An optional JSON schema for validating the list's items
	* @param array|null $indexes An optional array of indexes. Each index should be an associative
	*	array with the following keys: "name", "path", "type" and "default_descending"
	*
	* @return \Jsonpad\Resource\ItemList The list that was created
	*/
	public function createList($name, $realtimeEnabled = false, $schema = null, $indexes = null) {
		list($status, $response, $responseHeaders) = ApiConnector::request(
			$this->_username,
			$this->_apiToken,
			"POST",
			Resource\ItemList::$resourcePath,
			null,
			array(
				"name" => $name,
				"realtime_enabled" => $realtimeEnabled,
				"schema" => $schema,
				"indexes" => $indexes
			)
		);
		return new Resource\ItemList($this, $response[Resource\ItemList::$resourceName]);
	}
	
	/**
	* Get a list by name
	*
	* @param string $name The name of the list to fetch
	*
	* @return \Jsonpad\Resource\ItemList The requested list
	*/
	public function fetchList($name) {
		if (empty($name)) {
			throw new InvalidArgumentException("No list name specified");
		}
		$requestHeaders = null;
		
		// Check if the list is already in the cache
		$cacheResult = $this->listCache->check($name);
		if ($cacheResult !== null) {
			$requestHeaders = array(
				"If-Modified-Since" => $cacheResult[ListCache::LAST_MODIFIED]
			);
		}
		
		// Request the list from the API
		list($status, $response, $responseHeaders) = ApiConnector::request(
			$this->_username,
			$this->_apiToken,
			"GET",
			Resource\ItemList::$resourcePath . "/" . $name,
			null,
			null,
			$requestHeaders
		);
		
		// If a 304 (not modified) status is returned, then the list hasn't been modified since it
		// was added to the cache, so return it as-is
		if ($status == 304) {
			return $cacheResult[ListCache::LIST_INSTANCE];
		}
		
		// Otherwise, create a list instance from the returned data and add it to the cache
		$list = new Resource\ItemList($this, $response[Resource\ItemList::$resourceName]);
		$this->listCache->add($list, $responseHeaders["Last-Modified"]);
		return $list;
	}
	
	/**
	* Get a page of lists
	*
	* @param int $page The page of lists to return
	* @param int|null $pageSize The number of lists per page, if this isn't specified then the
	*	default page size will be used instead
	* @param string|null $sort The field to sort lists by (this can be "name", "item_count",
	*	"created" or "updated"), if this isn't specified the default sort field for lists will be
	*	used instead
	* @param bool|null $descending True if the lists should be sorted in descending order. If this
	*	is null, the default ordering mode for lists will be used
	* @param int &$total Outputs the total number of events
	*
	* @return \Jsonpad\Resource\ItemList[] A page of lists
	*/
	public function fetchLists(
		$page,
		$pageSize = null,
		$sort = null,
		$descending = null,
		&$total = null
	) {
		// Set the default page size if it isn't specified
		if (!isset($pageSize)) {
			$pageSize = Resource\ResourceBase::$pageSize;
		}
		
		// Set the paging parameters
		$parameters = array();
		$parameters["page"] = $page;
		$parameters["page_size"] = $pageSize;
		
		// If the sorting mode is set (ie. not null), set the sort parameter
		if ($sort !== null) {
			$parameters["sort"] = $sort;
		}
		
		// If descending is set (ie. not null), set the descending parameter
		if ($descending !== null) {
			$parameters["descending"] = $descending === true ? "1" : "0";
		}
		
		// Request the lists from the API
		list($status, $response, $responseHeaders) = ApiConnector::request(
			$this->_username,
			$this->_apiToken,
			"GET",
			Resource\ItemList::$resourcePath,
			$parameters
		);
		
		// Output the total number of lists
		if (isset($total)) {
			$total = $response["total"];
		}
		
		// Create list instances for each returned list
		$lists = array();
		foreach ($response[Resource\ItemList::$resourceCollectionName] as $list) {
			$lists[] = new Resource\ItemList($this, $list);
		}
		return $lists;
	}
	
	/**
	* Get a particular event, as specified by the event's id
	*
	* @param string $id The id of the event to get
	*
	* @return \Jsonpad\Resource\Event The requested event
	*/
	public function fetchEvent($id) {
		if (empty($id)) {
			throw new InvalidArgumentException("No event id specified");
		}
		list($status, $response, $responseHeaders) = ApiConnector::request(
			$this->_username,
			$this->_apiToken,
			"GET",
			Resource\Event::$resourcePath . "/" . $id
		);
		return new Resource\Event($this, $response[Resource\Event::$resourceName]);
	}
	
	/**
	* Get a page of events
	*
	* @param int $page The page of events to return
	* @param int|null $pageSize The number of events per page, if this isn't specified then the
	*	default page size will be used instead
	* @param bool|null $descending True if the events should be sorted in descending order. If this
	*	is null, the default ordering mode for events will be used
	* @param int &$total Outputs the total number of events
	*
	* @return \Jsonpad\Resource\Event[] A page of events
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
			$pageSize = Resource\ResourceBase::$pageSize;
		}
		
		// If descending is set (ie. not null), set the descending parameter
		if ($descending !== null) {
			$parameters["descending"] = $descending === true ? "1" : "0";
		}
		
		// Set the page and pagesize parameters
		$parameters["page"] = $page;
		$parameters["page_size"] = $pageSize;
		
		// Request the events from the API
		list($status, $response, $responseHeaders) = ApiConnector::request(
			$this->_username,
			$this->_apiToken,
			"GET",
			Resource\Event::$resourcePath,
			$parameters
		);
		
		// Output the total number of events
		if (isset($total)) {
			$total = $response["total"];
		}
		
		// Create event instances for each returned event
		$events = array();
		foreach ($response[Resource\Event::$resourceCollectionName] as $event) {
			$events[] = new Resource\Event($this, $event);
		}
		return $events;
	}
}

?>