<?php

namespace Mikeevstropov\Guzzle;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mikeevstropov\Guzzle\Transfer\Transfer;
use Mikeevstropov\Guzzle\Transfer\TransferStack;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

class ClientTest extends TestCase
{
    protected $url = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->url['200'] = getenv('url_200');
        $this->url['403'] = getenv('url_403');
        $this->url['503'] = getenv('url_503');
        $this->url['not_resolved'] = getenv('url_not_resolved');
    }

    public function testCanCreate()
    {
        new Client();
    }

    public function testCanRequest()
    {
        $client = new Client();

        $response = $client->request(
            'GET',
            $this->url['200']
        );

        Assert::isInstanceOf(
            $response,
            Response::class
        );
    }

    public function testCannotRequestOnClientException()
    {
        $client = new Client();

        try {

            $client->request(
                'GET',
                $this->url['403']
            );

        } catch (ClientException $exception) {}

        Assert::true(
            isset($exception)
        );
    }

    public function testCannotRequestOnServerException()
    {
        $client = new Client();

        try {

            $client->request(
                'GET',
                $this->url['503']
            );

        } catch (ServerException $exception) {}

        Assert::true(
            isset($exception)
        );
    }

    public function testCannotRequestOnConnectException()
    {
        $client = new Client();

        try {

            $client->request(
                'GET',
                $this->url['not_resolved']
            );

        } catch (ConnectException $exception) {}

        Assert::true(
            isset($exception)
        );
    }

    public function testCanRequestRepeatable()
    {
        $client = new Client();

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['200'],
            []
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );

        $lastTransfer = $transferStack->getLastTransfer();

        Assert::isInstanceOf(
            $lastTransfer,
            Transfer::class
        );

        $request = $lastTransfer->getRequest();

        Assert::isInstanceOf(
            $request,
            Request::class
        );

        $response = $lastTransfer->getResponse();

        Assert::isInstanceOf(
            $response,
            Response::class
        );

        Assert::same(
            $response->getStatusCode(),
            200
        );
    }

    public function testCanRequestRepeatableOnClientException()
    {
        $client = new Client();

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['403'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );

        $lastTransfer = $transferStack->getLastTransfer();

        Assert::isInstanceOf(
            $lastTransfer,
            Transfer::class
        );

        $request = $lastTransfer->getRequest();

        Assert::isInstanceOf(
            $request,
            Request::class
        );

        $response = $lastTransfer->getResponse();

        Assert::isInstanceOf(
            $response,
            Response::class
        );

        Assert::same(
            $response->getStatusCode(),
            403
        );
    }

    public function testCanRequestRepeatableOnServerException()
    {
        $client = new Client();

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['503'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );

        $lastTransfer = $transferStack->getLastTransfer();

        Assert::isInstanceOf(
            $lastTransfer,
            Transfer::class
        );

        $request = $lastTransfer->getRequest();

        Assert::isInstanceOf(
            $request,
            Request::class
        );

        $response = $lastTransfer->getResponse();

        Assert::isInstanceOf(
            $response,
            Response::class
        );

        Assert::same(
            $response->getStatusCode(),
            503
        );
    }

    public function testCanRequestRepeatableOnConnectException()
    {
        $client = new Client();

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['not_resolved'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );

        $lastTransfer = $transferStack->getLastTransfer();

        Assert::isInstanceOf(
            $lastTransfer,
            Transfer::class
        );

        Assert::isInstanceOf(
            $lastTransfer->getRequest(),
            Request::class
        );

        Assert::null(
            $lastTransfer->getResponse()
        );
    }

    public function testCanSetOptionRequestsLimit()
    {
        $value = 3;

        $client = new Client([
            'requests_limit' => $value
        ]);

        Assert::same(
            $client->getConfig('requests_limit'),
            $value
        );
    }

    public function testCannotSetOptionRequestsLimit()
    {
        try {

            new Client([
                'requests_limit' => -1
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'requests_limit' => 0
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'requests_limit' => false
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'requests_limit' => 'string'
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'requests_limit' => []
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'requests_limit' => new \stdClass()
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );
    }

    public function testCanSetOptionRepeatOn()
    {
        $value = [0, 4, 5];

        $client = new Client([
            'repeat_on' => $value
        ]);

        Assert::same(
            $client->getConfig('repeat_on'),
            $value
        );
    }

    public function testCannotSetOptionRepeatOn()
    {
        try {

            new Client([
                'repeat_on' => -1
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'repeat_on' => 0
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'repeat_on' => false
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'repeat_on' => 'string'
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );

        unset($exception);

        try {

            new Client([
                'repeat_on' => [-1]
            ]);

        } catch (\InvalidArgumentException $exception) {}

        Assert::true(
            isset($exception)
        );
    }

    public function testCanRepeatRequestOnClientException()
    {
        $client = new Client([
            'requests_limit' => 3,
            'repeat_on' => [4]
        ]);

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['403'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            3
        );
    }

    public function testCannotRepeatRequestOnClientException()
    {
        $client = new Client([
            'requests_limit' => 3,
            'repeat_on' => []
        ]);

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['403'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );
    }

    public function testCanRepeatRequestOnServerException()
    {
        $client = new Client([
            'requests_limit' => 3,
            'repeat_on' => [5]
        ]);

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['503'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            3
        );
    }

    public function testCannotRepeatRequestOnServerException()
    {
        $client = new Client([
            'requests_limit' => 3,
            'repeat_on' => []
        ]);

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['503'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );
    }

    public function testCanRepeatRequestOnConnectException()
    {
        $client = new Client([
            'requests_limit' => 3,
            'repeat_on' => [0]
        ]);

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['not_resolved'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            3
        );
    }

    public function testCannotRepeatRequestOnConnectException()
    {
        $client = new Client([
            'requests_limit' => 3,
            'repeat_on' => []
        ]);

        $requestRepeatable = new \ReflectionMethod(
            Client::class,
            'requestRepeatable'
        );

        $requestRepeatable->setAccessible(true);

        $transferStack = new TransferStack();

        $requestRepeatable->invokeArgs($client, [
            &$transferStack,
            'GET',
            $this->url['not_resolved'],
            ['http_errors' => false]
        ]);

        Assert::count(
            $transferStack->all(),
            1
        );
    }
}