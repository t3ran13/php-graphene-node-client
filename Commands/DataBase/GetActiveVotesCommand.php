<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetActiveVotesCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_active_votes';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //author
        '1' => ['string'], //permlink
    ];
}