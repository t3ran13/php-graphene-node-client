<?php


namespace GrapheneNodeClient\Commands;


class GetDiscussionsByCreatedCommand extends CommandAbstract
{
    protected $method = 'get_discussions_by_created';
    protected $requiredParams = [
        0 => ['tag', 'limit']
    ];
}