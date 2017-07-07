<?php

namespace Mikeevstropov\Guzzle\Transfer;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Transfer implements TransferInterface
{
    /**
     * @var Response
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Request $request = null,
        Response $response = null
    ) {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(Response $response = null)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }
}