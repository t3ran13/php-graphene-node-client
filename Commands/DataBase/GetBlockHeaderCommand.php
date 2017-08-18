<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetBlockHeaderCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_block_header';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['integer'], //block_id
    ];
}