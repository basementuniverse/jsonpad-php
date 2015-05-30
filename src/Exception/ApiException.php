<?php

namespace Jsonpad\Exception;

use Exception;

class ApiException extends Exception {
	private $_statusCode;
	private $_validationErrors;
	
	/**
	* Create a new API exception
	*
	* @param array $data The error data returned from the jsonpad API
	*/
	public function __construct($data) {
		parent::__construct(
			isset($data["message"]) ? $data["message"] : "",
			isset($data["error_code"]) ? $data["error_code"] : 0
		);
		$this->_statusCode = isset($data["status_code"]) ? $data["status_code"] : 0;
		$this->_validationErrors = isset($data["validation_errors"]) ? $data["validation_errors"] : 0;
	}
	
	/**
	* Get the HTTP status code for this exception
	*
	* @return int The HTTP status code
	*/
	public function getStatusCode() {
		return $this->_statusCode;
	}
}

?>