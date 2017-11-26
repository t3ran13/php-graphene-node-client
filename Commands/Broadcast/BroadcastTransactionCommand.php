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
        '0:ref_block_num'    => ['integer'], //tx ref_block_num = head_block_number & 0xFFFF
        '0:ref_block_prefix' => ['integer'], //tx ref_block_prefix = new Buffer(properties.head_block_id, 'hex').readUInt32LE(4),
        '0:expiration'       => ['string'], //tx expiration in ISO format YYYY-MM-DDTHH:mm:ss.sss
        '0:operations:*:0'   => ['string'], //tx operation name, example - 'vote'
        '0:operations:*:1'   => ['array'], //tx options, example - author, permlink and ect.
        '0:extensions'       => ['array'], //tx empty array while
        '0:signatures'       => ['array'] //tx
    ];
}