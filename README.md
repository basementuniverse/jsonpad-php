# jsonpad PHP library

## Requirements

PHP 5.3.3+

## Composer

You can install this library using [Composer](http://getcomposer.org/). Add the following requirement to 'composer.json':

	{
		"require": {
			"jsonpad/jsonpad-php": "1.*"
		}
	}

Then run:

	composer install

And in your PHP code, include the Composer autoloader:

	require_once("vendor/autoload.php");

## Manual Installation

To install this library manually, just download the [latest release](https://github.com/basementuniverse/jsonpad/releases).

Then, include the 'init.php' file in your PHP code:

	require_once("/jsonpad-php/init.php");

## Documentation

Here's an example of how to get started:

	$jsonpad = new \Jsonpad\Jsonpad("username", "apitoken");
	$list = $jsonpad->fetchList("testlist");
	$items = $list->fetchItems(1, 5);
	if (count($items) > 0) {
		var_dump($items[0]->getData());
	}

## Tests

Tests are provided in the '/tests/' folder. These will work with [PHPUnit](http://packagist.org/packages/phpunit/phpunit).