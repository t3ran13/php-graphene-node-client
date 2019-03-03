<?php


namespace GrapheneNodeClient\Commands\Single;

/**
 * Class BroadcastTransactionCommand
 *
 * This call will return before the transaction is included in a block.
 *
 */
class GetTransactionHexCommand extends CommandAbstract
{
    protected $method       = 'get_transaction_hex';
}