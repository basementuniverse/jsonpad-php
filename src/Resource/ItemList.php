<?php

namespace Jsonpad\Resource;

use InvalidArgumentException;

class ItemList extends MutableResourceBase {
	public static $resourceName = "list";
	public static $resourceCollectionName = "lists";
	public static $resourcePath = "lists";
	
	private $_liveName;
	private $_name;
	private $_itemCount;
	private $_indexCount;
	private $_dataSize;
	private $_hasSchema;
	private $_realtimeEnabled;
	private $_created;
	private $_updated;
	
	/**
	* Create a new list instance from the specified list data
	*
	* @param \Jsonpad\Jsonpad $jsonpad The API connector containing the credentials to use when
	*	saving changes to this resource
	* @param array $list The list data
	*/
	public function __construct($jsonpad, $list) {
		$this->_jsonpad = $jsonpad;
		$this->_liveName = $this->_name = parent::_getDataProperty("name", $list);
		$this->_itemCount = parent::_getDataProperty("item_count", $list);
		$this->_indexCount = parent::_getDataProperty("index_count", $list);
		$this->_dataSize = parent::_getDataProperty("data_size", $list);
		$this->_hasSchema = parent::_getDataProperty("has_schema", $list);
		$this->_realtimeEnabled = parent::_getDataProperty("realtime_enabled", $list);
		$this->_created = parent::_getDataProperty("created", $list);
		$this->_updated = parent::_getDataProperty("updated", $list);
	}
	
	/**
	* Get the API path for this list
	*
	* @return string The API path for this list
	*/
	public function getApiPath() {
		return self::$resourcePath . "/" . $this->_liveName;
	}
	
	/**
	* Get the name for this list
	*
	* @return string The list's name
	*/
	public function getName() {
		return $this->_name;
	}
	
	/**
	* Rename this list
	*
	* @param string $name The new name for this list
	*/
	public function setName($name) {
		if (is_string($name)) {
			$this->_name = (string)$name;
			$this->_dirty = true;
		}
	}
	
	/**
	* Get the number of items contained in this list
	*
	* @return int The number of items contained in this list
	*/
	public function getItemCount() {
		return $this->_itemCount;
	}
	
	/**
	* Get the number of indexes in this list
	*
	* @return int The number of indexes in this list
	*/
	public function getIndexCount() {
		return $this->_indexCount;
	}
	
	/**
	* Get the item data size for all items contained in this list
	*
	* @return int The item data size for all items contained in this list
	*/
	public function getDataSize() {
		return $this->_dataSize;
	}
	
	/**
	* Check if this list has a JSON schema or not
	*
	* @return bool True if this list has a JSON schema
	*/
	public function getHasSchema() {
		return $this->_hasSchema;
	}
	
	/**
	* Get the realtime enabled state for this list
	*
	* @return bool True if realtime events are enabled for this list
	*/
	public function getRealtimeEnabled() {
		return $this->_realtimeEnabled;
	}
	
	/**
	* Set the realtime enabled state for this list
	*
	* @param bool $realtimeEnabled True if realtime events should be enabled for this list
	*/
	public function setRealtimeEnabled($realtimeEnabled) {
		if (is_bool($realtimeEnabled)) {
			$this->_realtimeEnabled = (bool)$realtimeEnabled;
			$this->_dirty = true;
		}
	}
	
	/**
	* Get the created date/time for this list in ISO 8601 format
	*
	* @return string The list's created date/time
	*/
	public function getCreated() {
		return $this->_created;
	}
	
	/**
	* Get the last updated date/time for this list in ISO 8601 format
	*
	* @return string The list's last updated date/time
	*/
	public function getUpdated() {
		return $this->_updated;
	}
	
	/**
	* Save this list's current state to the API
	*/
	public function save() {
		parent::_save(array(
			"name" => $this->_name,
			"realtime_enabled" => $this->_realtimeEnabled
		));
		$this->_liveName = $this->_name;
	}
	
	/**
	* Reload this list from the API
	*/
	public function refresh() {
		$list = parent::refresh();
		$this->_liveName = $this->_name = $list["name"];
		$this->_itemCount = $list["item_count"];
		$this->_indexCount = $list["index_count"];
		$this->_dataSize = $list["data_size"];
		$this->_hasSchema = $list["has_schema"];
		$this->_realtimeEnabled = $list["realtime_enabled"];
		$this->_created = $list["created"];
		$this->_updated = $list["updated"];
	}
	
	/**
	* Create an item in this list
	*
	* @param mixed|null $data The item's data
	*
	* @return \Jsonpad\Resource\Item The item that was created
	*/
	public function createItem($data) {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"POST",
			$this->getApiPath() . "/" . Item::$resourcePath,
			null,
			$data
		);
		return new Item($this->_jsonpad, $this, $response[Item::$resourceName]);
	}
	
	/**
	* Delete this list and all of the items contained in this list
	*/
	public function delete() {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"DELETE",
			$this->getApiPath()
		);
	}
	
	/**
	* Delete all of the items contained in this list
	*/
	public function deleteItems() {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"DELETE",
			$this->getApiPath() . "/" . Item::$resourcePath
		);
	}
	
	/**
	* Get the JSON schema associated with this list, or null if there is no schema
	*
	* @return mixed|null The JSON schema for this list, or null if this list doesn't have a schema
	*/
	public function fetchSchema() {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath() . "/" . ItemListSchema::$resourcePath
		);
		return new ItemListSchema($this->_jsonpad, $this, $response[ItemListSchema::$resourceName]);
	}
	
	/**
	* Get a particular index for this list
	*
	* @param string $name The name of the index to get
	*
	* @return \Jsonpad\Resource\ItemListIndex The requested index
	*/
	public function fetchIndex($name) {
		if (empty($name)) {
			throw new InvalidArgumentException("No index name specified");
		}
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath() . "/" . ItemListIndex::$resourcePath . "/" . $name
		);
		return new ItemListIndex($this->_jsonpad, $this, $response[ItemListIndex::$resourceName]);
	}
	
	/**
	* Get all indexes for this list
	*
	* @param int &$total Outputs the total number of indexes
	*
	* @return \Jsonpad\Resource\ItemListIndex[] An array of all indexes for this list
	*/
	public function fetchIndexes(&$total = null) {
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath() . "/" . ItemListIndex::$resourcePath
		);
		
		// Output the total number of indexes
		if (isset($total)) {
			$total = $response["total"];
		}
		
		// Create index instances for each returned index
		$indexes = array();
		foreach ($response[ItemListIndex::$resourceCollectionName] as $index) {
			$indexes[] = new ItemListIndex($this->_jsonpad, $this, $index);
		}
		return $indexes;
	}
	
	/**
	* Get a particular item from this list
	*
	* @param string $id The id of the item to get
	*
	* @return \Jsonpad\Resource\Item The requested item
	*/
	public function fetchItem($id) {
		if (empty($id)) {
			throw new InvalidArgumentException("No item id specified");
		}
		$requestHeaders = null;
		
		// Check if the item is already in the cache
		$cacheResult = $this->_jsonpad->itemCache->check($id);
		if ($cacheResult !== null) {
			$requestHeaders = array(
				"If-None-Match" => $cacheResult[\Jsonpad\ItemCache::ETAG]
			);
		}
		
		// Request the item from the API
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath() . "/" . Item::$resourcePath . "/" . $id,
			null,
			null,
			$requestHeaders
		);
		
		// If a 304 (not modified) status is returned, then the item has the same data contents as
		// the item stored in the cache, so return it as-is
		if ($status == 304) {
			return $cacheResult[\Jsonpad\ItemCache::ITEM_INSTANCE];
		}
		
		// Otherwise, create an item instance from the returned data and add it to the cache
		$item = new Item($this->_jsonpad, $this, $response[Item::$resourceName]);
		$this->_jsonpad->itemCache->add($item, $responseHeaders["ETag"]);
		return $item;
	}
	
	/**
	* Get a page of items in this list
	*
	* @param int $page The page of items to return
	* @param int $pageSize The number of items per page, if this isn't specified then the default
	*	page size will be used instead
	* @param string $sort The field to sort items by. This should be a valid item sort field like
	*	"index", "created" or "updated", or it should be the name of one of the containing list's
	*	indexes
	* @param bool|null $descending True if the items should be sorted in descending order. If this
	*	is null, the default ordering mode for items (or for the specified index) will be used
	* @param string $filterName The new name of the index to filter on
	* @param string $filterValue The value to filter items by, this will only be used if a filter
	*	name is specified
	* @param int &$total Outputs the total number of items
	*
	* @return \Jsonpad\Resource\Event[] A page of events for this list
	*/
	public function fetchItems(
		$page,
		$pageSize = null,
		$sort = null,
		$descending = null,
		$filterName = null,
		$filterValue = null,
		&$total = null
	) {
		// Set the default page size if it isn't specified
		if (!isset($pageSize)) {
			$pageSize = ResourceBase::$pageSize;
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
		
		// If a filter name and value is set (ie. $filterName and $filterValue are not null), set
		// the named filter parameter
		if ($filterName !== null && $filterValue !== null) {
			$parameters[$filterName] = $filterValue;
		}
		
		// Request the items from the API
		list($status, $response, $responseHeaders) = \Jsonpad\ApiConnector::request(
			$this->_jsonpad->getUsername(),
			$this->_jsonpad->getApiToken(),
			"GET",
			$this->getApiPath() . "/" . Item::$resourcePath,
			$parameters
		);
		
		// Output the total number of item
		if (isset($total)) {
			$total = $response["total"];
		}
		
		// Create item instances for each returned item
		$items = array();
		foreach ($response[Item::$resourceCollectionName] as $item) {
			$items[] = new Item($this->_jsonpad, $this, $item);
		}
		return $items;
	}
}

?>