<?php

namespace Jsonpad;

class ApiConnectorTest extends TestCase {
	
	/**
	* Send a test request, expected response is 200 with a version property set to "1.0"
	*/
	public function testRequest() {
		list($status, $response, $responseHeaders) = ApiConnector::request(
			parent::$username,
			parent::$apiToken,
			"GET",
			"version"
		);
		$this->assertSame($status, 200);
		$this->assertSame($response["version"], "1.0");
		
		// Also check one of the headers to make sure headers are being returned
		$this->assertArrayHasKey("Access-Control-Allow-Origin", $responseHeaders);
	}
	
	/**
	* Send an unknown request, should throw ApiException
	*
	* @expectedException \Jsonpad\Exception\ApiException
	* @expectedExceptionMessage Unknown request
	*/
	public function testUnknownRequest() {
		list($status, $response, $responseHeaders) = ApiConnector::request(
			parent::$username,
			parent::$apiToken,
			"GET",
			"wibble"	// On second thoughts, I should probably implement this endpoint...
		);
	}
	
	/**
	* Send a test request with no username, should throw InvalidArgumentException
	*
	* @expectedException InvalidArgumentException
	* @expectedExceptionMessage Username is not defined
	*/
	public function testRequestNoUsername() {
		list($status, $response, $responseHeaders) = ApiConnector::request(
			"",
			parent::$apiToken,
			"GET",
			"version"
		);
	}
	
	/**
	* Send a test request with no API token, should throw InvalidArgumentException
	*
	* @expectedException InvalidArgumentException
	* @expectedExceptionMessage API token is not defined
	*/
	public function testRequestNoApiToken() {
		list($status, $response, $responseHeaders) = ApiConnector::request(
			parent::$username,
			"",
			"GET",
			"version"
		);
	}
	
	/**
	* Send a test request with an incorrect token, should throw AuthenticationException
	*
	* @expectedException \Jsonpad\Exception\AuthenticationException
	* @expectedExceptionMessage Access denied
	*/
	public function testRequestIncorrectToken() {
		list($status, $response, $responseHeaders) = ApiConnector::request(
			parent::$username,
			"wibble",
			"GET",
			"version"
		);
	}
}

?>