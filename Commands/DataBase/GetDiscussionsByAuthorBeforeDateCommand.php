<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetDiscussionsByAuthorBeforeDateCommand extends CommandAbstract
{
    protected $method         = 'get_discussions_by_author_before_date';
    protected $queryDataMap = [
        '0' => ['string'], //'author',
        '1' => ['string'], //'start_permlink' for pagination,
        '2' => ['string'], //'before_date'
        '3' => ['integer'], //'limit'
    ];
}