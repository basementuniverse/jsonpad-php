<?php

namespace Jsonpad;

use InvalidArgumentException;

class ApiConnector {
	const HTTP_STATUS = 0;	// Array indexes for data returned from request()
	const RESPONSE = 1;
	const HEADERS = 2;
	
	private static $_baseUrl = "https://jsonpad.io/api/v1.0";
	
	/**
	* Send an API request via curl
	*
	* @param string $username The account username to use when authenticating the request
	* @param string $apiToken The API token to use when authenticating the request
	* @param string $method The HTTP method to use ("GET", "POST", "PUT", "PATCH", "DELETE"), not
	*	case sensitive, if this isn't a valid method string then a GET request will be sent
	* @param string $path The request path (will be appended to the base URL)
	* @param array|null $params An associative array of querystring parameters and their values
	* @param array|null $data This will be encoded as JSON and sent as POST data with the request
	* @param array|null $headers An associative array of headers and their values
	*
	* @throws InvalidArgumentException if the username is not defined
	* @throws InvalidArgumentException if the API token is not defined
	* @throws \Jsonpad\Exception\ConnectionException if an error occurs while connecting to the API
	* @throws \Jsonpad\Exception\AuthenticationException if the API responds with a 401 status code
	* @throws \Jsonpad\Exception\ApiException if the API responds with any other error status code
	*
	* @return array An array containing 3 elements: the response HTTP status code, the response
	*	body (an associative array or null if no data was returned) and an associative array of
	*	response headers and their values
	*/
	public static function request(
		$username,
		$apiToken,
		$method,
		$path,
		$params = null,
		$data = null,
		$headers = null
	) {
		if (empty($username)) {
			throw new InvalidArgumentException("Username is not defined");
		}
		if (empty($apiToken)) {
			throw new InvalidArgumentException("API token is not defined");
		}
		
		// Initialise curl session
		$curl = curl_init();
		$options = array();
		$requestHeaders = array();
		$responseHeaders = array();
		
		// Prepare method
		switch (strtolower($method)) {
			case "post":	$options[CURLOPT_POST] = 1;					break;
			case "put":		$options[CURLOPT_CUSTOMREQUEST] = "put";	break;
			case "delete":	$options[CURLOPT_CUSTOMREQUEST] = "delete";	break;
			case "patch":	$options[CURLOPT_CUSTOMREQUEST] = "patch";	break;
			default:		$options[CURLOPT_HTTPGET] = 1;				break;
		}
		
		// Prepare absolute request url
		$url = self::$_baseUrl . "/" . $path;
		if (is_array($params) && count($params) > 0) {
			array_walk($params, function(&$v, $k) { $v = $k . "=" . $v; });
			$url .= "?" . implode("&", $params);
		}
		$options[CURLOPT_URL] = $url;
		
		// Prepare data
		if (isset($data)) {
			$options[CURLOPT_POSTFIELDS] = json_encode($data);
		}
		
		// Prepare headers
		if (is_array($headers) && count($headers) > 0) {
			foreach ($headers as $k => $v) {
				$requestHeaders[] = $k . ": " . $v;
			}
		}
		$requestHeaders[] = "Accept: application/json";
		$requestHeaders[] = "Content-Type: application/json";
		
		// Set request options
		// SSL certificate
		$options[CURLOPT_SSL_VERIFYPEER] = true;
		$options[CURLOPT_SSL_VERIFYHOST] = 2;
		$options[CURLOPT_CAINFO] = dirname(__FILE__) . "/../data/cacert.pem";
		
		// Basic authentication
		$options[CURLOPT_USERPWD] = $username . ":" . $apiToken;
		
		// Headers
		$options[CURLOPT_HTTPHEADER] = $requestHeaders;
		
		// Timeouts
		$options[CURLOPT_CONNECTTIMEOUT] = 30;
		$options[CURLOPT_TIMEOUT] = 30;
		
		// Response options
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_HEADERFUNCTION] = self::_getHeaderFunction($responseHeaders);
		curl_setopt_array($curl, $options);
		
		// Send request
		$response = curl_exec($curl);
		
		// Check for curl errors
		$errorCode = curl_errno($curl);
		if ($errorCode !== 0) {
			$errorMessage = curl_error($curl);
			curl_close($curl);
			throw new Exception\ConnectionException($errorMessage, $errorCode);
		}
		
		// Get the HTTP response code and the parse the response body as JSON
		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$responseBody = json_decode($response, true);
		curl_close($curl);
		
		// Check for HTTP error status
		if ($responseCode >= 400) {
			// If the response code is 401 (unauthorized), throw an authentication exception
			if ($responseCode == 401) {
				throw new Exception\AuthenticationException(
					isset($responseBody["message"]) ? $responseBody["message"] : "",
					isset($responseBody["error_code"]) ? $responseBody["error_code"] : null
				);
			}
			
			// If the response code is 429 (rate limit exceeded), throw a rate limit exception
			if ($responseCode == 429) {
				throw new Exception\RateLimitException($responseBody);
			}
			
			// Otherwise throw an API exception
			throw new Exception\ApiException($responseBody);
		}
		
		// Request was successful, so return the response code, body and headers
		return array(
			self::HTTP_STATUS => $responseCode,
			self::RESPONSE => $responseBody,
			self::HEADERS => $responseHeaders
		);
	}
	
	/**
	* Get a function for writing HTTP response headers to the specified array
	*
	* Only headers containing a key/value pair (separated by ":") will be written, the resulting
	* array will be associative, with each element corresponding to a header name and value
	*
	* @param array &$headers The array in which to write headers
	*
	* @return callable A function for writing HTTP response headers. The function will take 2
	*	arguments (the curl session and a header string), and returns the number of bytes in the
	*	header string
	*/
	private static function _getHeaderFunction(&$headers) {
		return function($curl, $header) use (&$headers) {
			if (!empty($header) && strpos($header, ":") !== false) {
				list($k, $v) = array_map("trim", explode(":", $header, 2));
				$headers[$k] = $v;
			}
			return mb_strlen($header, "8bit");
		};
	}
}

?>