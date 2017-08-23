<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetAccountVotesCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_account_votes';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //account name
    ];
}