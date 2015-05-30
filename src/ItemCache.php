<?php

namespace Jsonpad;

class ItemCache {
	const ITEM_INSTANCE = 0;
	const ETAG = 1;
	
	private $_cache = array();
	
	/**
	* Check if an item exists in the cache
	*
	* @param string $id The id of the item to check for
	*
	* @return array|null An array containing the item and the item's etag (data hash) if the item
	*	was found in the cache, otherwise null
	*/
	public function check($id) {
		if (array_key_exists($id, $this->_cache)) {
			return $this->_cache[$id];
		}
		return null;
	}
	
	/**
	* Add an item to the cache alongside the item's etag, or update the item and it's etag if it's
	* already in the cache
	*
	* @param \Jsonpad\Resource\Item $item The item to add
	* @param string $etag The item's etag, as returned in the ETag header
	*/
	public function add($item, $etag) {
		$this->_cache[$item->getId()] = array(
			self::ITEM_INSTANCE => $item,
			self::ETAG => $etag
		);
	}
	
	/**
	* Clear the cache
	*/
	public function clear() {
		$this->_cache = array();
	}
}

?>