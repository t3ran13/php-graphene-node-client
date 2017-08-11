<?php


namespace GrapheneNodeClient\Commands\Broadcast;

/**
 * Class BroadcastTransactionCommand
 *
 * This call will return before the transaction is included in a block.
 *
 * @package GrapheneNodeClient\Commands\Broadcast
 */
class BroadcastTransactionCommand extends CommandAbstract
{
    protected $method       = 'broadcast_transaction';
    protected $queryDataMap = [
        'ref_block_num'    => ['string'], //tx ref_block_num = head_block_number & 0xFFFF
        'ref_block_prefix' => ['string'], //tx ref_block_prefix = new Buffer(properties.head_block_id, 'hex').readUInt32LE(4),
        'expiration'       => ['string'], //tx expiration in ISO format YYYY-MM-DDTHH:mm:ss.sss
        'operations:*:0'   => ['string'], //tx operation name, example - 'vote'
        'operations:*:1'   => ['array'], //tx options, example - author, permlink and ect.
        'extensions'       => ['array'], //tx empty array while
        'signatures'       => ['array'] //tx
    ];
}