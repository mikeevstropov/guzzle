<?php

namespace Mikeevstropov\Guzzle;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use Mikeevstropov\Guzzle\Transfer\Transfer;
use Mikeevstropov\Guzzle\Transfer\TransferStack;
use Webmozart\Assert\Assert;

class Client extends BaseClient
{
    public function __construct(array $config = [])
    {
        $this->setDefaults($config);

        $this->validateOptions($config);

        parent::__construct($config);
    }

    /**
     * Set defaults for passed configuration options
     *
     * @param array $options
     */
    protected function setDefaults(
        array &$options
    ) {
        $options['requests_limit'] = isset($options['requests_limit'])
            ? $options['requests_limit']
            : 1;

        $options['repeat_on'] = isset($options['repeat_on'])
            ? $options['repeat_on']
            : [0, 5];
    }

    /**
     * Validate the configuration options
     *
     * @param array $options
     */
    protected function validateOptions(
        array $options
    ) {
        Assert::keyExists(
            $options,
            'requests_limit',
            'Client option "requests_limit" not set by default.'
        );

        Assert::integer(
            $options['requests_limit'],
            'Client option "requests_limit" must be an integer, %s given.'
        );

        Assert::greaterThan(
            $options['requests_limit'],
            0,
            'Client option "requests_limit" must be greater than %2$d, %s given.'
        );

        Assert::keyExists(
            $options,
            'repeat_on',
            'Client option "repeat_on" not set by default.'
        );

        Assert::isArray(
            $options['repeat_on'],
            'Client option "repeat_on" must be an array, %s given.'
        );

        for ($i = 0, $l = count($options['repeat_on']); $i < $l; $i++) {

            $item = $options['repeat_on'][$i];

            Assert::integer(
                $item,
                'Client option "repeat_on" must contain array items that type is integer, %s given.'
            );

            Assert::greaterThanEq(
                $item,
                'Client option "repeat_on" must contain array items that greater than 0 or equal, %s given.'
            );
        }
    }

    /**
     * Merges default options into the array. This is the parent
     * method "prepareDefaults" that will set "protected"
     * visibility.
     *
     * @param array $options
     *
     * @return array
     */
    protected function prepareDefaults(
        array $options
    ) {
        $prepareDefaults = new \ReflectionMethod(
            parent::class,
            'prepareDefaults'
        );

        $prepareDefaults->setAccessible(true);

        $options = $prepareDefaults->invoke(
            $this,
            $options
        );

        $this->validateOptions($options);

        return $options;
    }

    /**
     * Request factory
     *
     * @param string $method  Request method
     * @param string $uri     Uri of request
     * @param array  $options Array of request options
     *
     * @return Request
     */
    protected function createRequest(
        $method,
        $uri = '',
        array $options = []
    ) {
        $headers = isset($options['headers']) ? $options['headers'] : [];
        $body    = isset($options['body']) ? $options['body'] : null;
        $version = isset($options['version']) ? $options['version'] : '1.1';

        return new Request(
            $method,
            $uri,
            $headers,
            $body,
            $version
        );
    }

    /**
     * {@inheritdoc}
     */
    public function request(
        $method,
        $uri = '',
        array $options = []
    ) {
        $transferStack = new TransferStack();

        $this->requestRepeatable(
            $transferStack,
            $method,
            $uri,
            $options
        );

        return $transferStack
            ->getLastTransfer()
            ->getResponse();
    }

    /**
     * Repeatable request
     *
     * @param TransferStack $transferStack Instance
     * @param string        $method        Request method
     * @param string        $uri           Request URI
     * @param array         $options       Request options
     *
     * @return TransferStack
     */
    protected function requestRepeatable(
        TransferStack &$transferStack,
        $method,
        $uri,
        array $options
    ) {
        $options = $this->prepareDefaults($options);

        $silent = !$options['http_errors'];

        $limit = $options['requests_limit'];

        $repeatOn = $options['repeat_on'];

        $options['http_errors'] = false;

        $this->validateOptions($options);

        $request = $this->createRequest(
            $method,
            $uri,
            $options
        );

        $response = null;

        $exception = null;

        for ($i = 0; $i < $limit; $i++) {

            $exception = null;

            try {

                $response = $this->send(
                    $request,
                    $options
                );

            } catch (TransferException $exception) {}

            $transfer = new Transfer(
                $request,
                $response
            );

            $transferStack->push(
                $transfer
            );

            if (
                $exception
                && array_search(0, $repeatOn, true) === false
            ) break;

            if (
                $response
                && array_search(
                    (int) floor($response->getStatusCode() / 100),
                    $repeatOn,
                    true
                ) === false
            ) break;

            if (
                $response
                && $response->getStatusCode() < 400
            ) break;
        }

        if (!$silent && $exception)
            throw $exception;

        if (!$silent && $response->getStatusCode() > 399)
            throw RequestException::create(
                $request,
                $response
            );

        return $transferStack;
    }
}