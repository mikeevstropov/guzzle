# mikeevstropov/guzzle

Retrying behavior for [Guzzle](https://packagist.org/packages/guzzlehttp/guzzle)

## Installation

Add dependency [mikeevstropov/guzzle](https://packagist.org/packages/mikeevstropov/guzzle)

```bash
$ composer require mikeevstropov/guzzle
```

## New options

- **requests_limit**
  
  Accepts:
  
  - `integer` - positive integer
  
  Default:
  
  - `1` - only one attempt
  
  _Maximum number of attempts to receive a response._

- **repeat_on**
  
  Accepts:
  
  - `array` - array with a numeric index
  
  Default:
  
  - `array(0, 5)` - repeat failed request or response code 5xx
  
  _List of error codes for retrying requests:_
  
    - `array(5)` - on `GuzzleHttp\Exception\ServerException` (5xx code)
    - `array(4)` - on `GuzzleHttp\Exception\ClientException` (4xx code)
    - `array(0)` - other `TransferException` like `GuzzleHttp\Exception\ConnectException`
  
  _You can combine it like `array(4, 5).`_
  
## Usage

```php
<?php

$client = new \GuzzleHttp\Client();

// Let's try to request "http://httpstat.us/503" that
// page will always return "503 Service Unavailable"
$response = $client->get('http://httpstat.us/503', [
    'requests_limit' => 3,
]); // will thrown GuzzleHttp\Exception\ServerException after 3 attempts

// We can pass option "repeat_on" to prevent retrying
// if response has code 5xx (by default [0, 5])
$response = $client->get('http://httpstat.us/503', [
    'requests_limit' => 3,
    'repeat_on' => array(0, 4)
]); // will thrown GuzzleHttp\Exception\ServerException after first request

// But same options with request to the page that return 4xx
// will have 3 attempts, because we pass "4" as array item in
// option "repeat_on"
$response = $client->get('http://httpstat.us/402', [
    'requests_limit' => 3,
    'repeat_on' => array(0, 4)
]); // will thrown GuzzleHttp\Exception\ServerException after 3 attempts

```

## Development

Clone

```bash
$ git clone https://github.com/mikeevstropov/guzzle.git
```

Go to project

```bash
$ cd guzzle
```

Install dependencies

```bash
$ composer install
```

Increase composer timeout. Since composer by default set it to 300 seconds.

```bash
$ composer config --global process-timeout 600
```

Run the tests

```bash
$ composer test
```