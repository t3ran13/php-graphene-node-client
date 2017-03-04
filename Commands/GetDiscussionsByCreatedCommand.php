<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetDiscussionsByCreatedCommand extends CommandAbstract
{
    protected $method = 'get_discussions_by_created';
    protected $requiredParams = [
        0 => ['tag', 'limit']
    ];
}