<?php


namespace GrapheneNodeClient\Commands\Single;

/**
 * Class BroadcastTransactionCommand
 *
 * This call will return before the transaction is included in a block.
 *
 */
class BroadcastTransactionCommand extends CommandAbstract
{
    protected $method       = 'broadcast_transaction';
}