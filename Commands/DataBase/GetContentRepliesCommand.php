<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetContentRepliesCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_content_replies';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //author
        '1' => ['string'], //permlink
    ];
}