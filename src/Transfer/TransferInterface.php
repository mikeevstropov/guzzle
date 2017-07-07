<?php

namespace Mikeevstropov\Guzzle\Transfer;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Interface TransferInterface
 *
 * Transfer instance will contain Request and Response
 * who can be received by getters.
 *
 * @package Mikeevstropov\Guzzle\Transfer
 */
interface TransferInterface
{
    /**
     * TransferInterface constructor.
     *
     * @param Request|null $request
     * @param Response|null $response
     */
    function __construct(
        Request $request = null,
        Response $response = null
    );

    /**
     * @param Request $request
     *
     * @return $this
     */
    function setRequest(Request $request = null);

    /**
     * @return null|Request
     */
    function getRequest();

    /**
     * @param Response $response
     *
     * @return $this
     */
    function setResponse(Response $response = null);

    /**
     * @return null|Response
     */
    function getResponse();
}