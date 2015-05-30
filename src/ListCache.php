<?php

namespace Jsonpad;

class ListCache {
	const LIST_INSTANCE = 0;
	const LAST_MODIFIED = 1;
	
	private $_cache = array();
	
	/**
	* Check if a list exists in the cache
	*
	* @param string $name The name of the list to check for
	*
	* @return array|null An array containing the list and the list's last modified date if the list
	*	was found in the cache, otherwise null
	*/
	public function check($name) {
		if (array_key_exists($name, $this->_cache)) {
			return $this->_cache[$name];
		}
		return null;
	}
	
	/**
	* Add a list to the cache alongside the list's last modified date, or update the list and it's
	* last modified date if it's already in the cache
	*
	* @param \Jsonpad\Resource\ItemList $list The list to add
	* @param string $lastModified The list's last modified date, as returned in the Last-Modified
	*	header (ie. in a format defined in RFC 2616)
	*/
	public function add($list, $lastModified) {
		$this->_cache[$list->getName()] = array(
			self::LIST_INSTANCE => $list,
			self::LAST_MODIFIED => $lastModified
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