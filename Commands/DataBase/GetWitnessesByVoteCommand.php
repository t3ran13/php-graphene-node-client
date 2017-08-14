<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetWitnessesByVoteCommand extends CommandAbstract
{
    protected $method       = 'get_witnesses_by_vote';
    protected $queryDataMap = [
        '0' => ['string'], //from accountName, can be empty string ''
        '1' => ['integer'] //limit
    ];
}