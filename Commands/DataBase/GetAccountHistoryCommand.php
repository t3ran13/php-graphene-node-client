<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetAccountHistoryCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_account_history';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //authors
        '1' => ['integer'], //from
        '2' => ['integer'], //limit max 2000
    ];
}