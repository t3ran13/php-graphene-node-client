<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetContentCommand extends CommandAbstract
{
    /** @var string  */
    protected $method = 'get_content';

    /** @var array  */
    protected $requiredParams = [
        0, //'author',
        1 //'permlink'
    ];
}