<?php


namespace GrapheneNodeClient\Commands\Single;


class GetBlockHeaderCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_block_header';
}