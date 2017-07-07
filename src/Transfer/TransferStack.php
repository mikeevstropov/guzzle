<?php

namespace Mikeevstropov\Guzzle\Transfer;

class TransferStack implements TransferStackInterface
{
    protected $transfers = array();

    /**
     * {@inheritdoc}
     */
    public function push(Transfer $transfer)
    {
        $this->transfers[] = $transfer;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastTransfer()
    {
        return end($this->transfers) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->transfers;
    }
}