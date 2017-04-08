<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetDiscussionsByBlogCommand extends CommandAbstract
{
    protected $method            = 'get_discussions_by_blog';
    protected $requiredParams = [
        ConnectorInterface::PLATFORM_GOLOS => [
            0 => [
                'select_authors', //'author',
                'limit', //'limit'
//          'start_author', //'start_author' for pagination,
//          'start_permlink', //'start_permlink' for pagination,
            ]
        ],
        ConnectorInterface::PLATFORM_STEEMIT => [
            0 => [
                'tag', //'author',
                'limit', //'limit'
//          'start_author', //'start_author' for pagination,
//          'start_permlink', //'start_permlink' for pagination,
            ]
        ]
    ];
}