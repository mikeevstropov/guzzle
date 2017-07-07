<?php

namespace Mikeevstropov\Guzzle\Transfer;

interface TransferStackInterface
{
    /**
     * Push Transfer instance to Transfer stack
     *
     * @param Transfer $transfer
     *
     * @return $this
     */
    function push(Transfer $transfer);

    /**
     * Get last Transfer instance
     *
     * @return null|Transfer
     */
    function getLastTransfer();

    /**
     * Return all Transfer instances
     *
     * @return array
     */
    function all();
}