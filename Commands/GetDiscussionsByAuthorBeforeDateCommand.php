<?php


namespace GrapheneNodeClient\Commands;


class GetDiscussionsByAuthorBeforeDateCommand extends CommandAbstract
{
    protected $method = 'get_discussions_by_author_before_date';
    protected $requiredParams = [
        0, //'author',
        1, //'start_permlink',
        2, //'before_date',
        3 //'limit'
    ];
}