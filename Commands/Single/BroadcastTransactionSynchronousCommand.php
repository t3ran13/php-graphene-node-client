<?php


namespace GrapheneNodeClient\Commands\Single;

/**
 * Class BroadcastTransactionSynchronousCommand
 *
 * This call will not return until the transaction is included in a block.
 *
 */
class BroadcastTransactionSynchronousCommand extends CommandAbstract
{
    protected $method       = 'broadcast_transaction_synchronous';
}