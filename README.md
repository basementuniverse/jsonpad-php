# jsonpad PHP library

> DEPRECATED
> This was designed for the old (~2015) version of jsonpad, which has since been rebuilt from the ground-up. This repo no longer works.

This is a PHP library for connecting to your [jsonpad](https://jsonpad.io) account, it allows you to read/write lists and items without having to communicate directly with the REST API.

It supports the jsonpad API caching features, so you can load lists and items multiple times without worrying too much about hitting the rate limits.

See the [jsonpad API Documentation](https://jsonpad.io/docs-home) for more information on the jsonpad API.

## Requirements

PHP 5.3.3+

## Install with Composer

You can install this library using [Composer](http://getcomposer.org/). Add the following requirement to `composer.json`:

	{
		"require": {
			"jsonpad/jsonpad-php": "1.*"
		}
	}

Then run:

	composer install

In your PHP code, include the Composer autoloader:

	require_once("vendor/autoload.php");

## Manual Installation

To install this library manually, just download the [latest release](https://github.com/basementuniverse/jsonpad-php/releases).

Then, include the `init.php` file in your PHP code:

	require_once("jsonpad-php/init.php");

## Documentation

Here's an example of how to get started:

	$jsonpad = new \Jsonpad\Jsonpad("username", "apitoken");
	$list = $jsonpad->fetchList("testlist");
	$items = $list->fetchItems(1, 5);
	if (count($items) > 0) {
		var_dump($items[0]->getData());
	}

*Full documentation coming soon.*

## Tests

[PHPUnit](http://packagist.org/packages/phpunit/phpunit) tests are provided in the `/tests/` folder.
