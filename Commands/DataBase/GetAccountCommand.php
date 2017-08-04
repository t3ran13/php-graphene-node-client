<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetAccountCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_accounts';

    /** @var array */
    protected $queryDataMap = [
        '0' => ['string'], //authors
    ];
}