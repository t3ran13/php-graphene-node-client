<?php


namespace GrapheneNodeClient\Debug;


use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Commands\DataBase\CommandAbstract;

class TestCommand extends CommandAbstract
{
    protected $method            = 'get_api_by_name';
    protected $queryDataMap = [
        ConnectorInterface::PLATFORM_GOLOS   => [
            '*'            => ['string']
        ],
        ConnectorInterface::PLATFORM_STEEMIT => [
            '*'            => ['string']
        ]
    ];
    protected $apiName = 'login_api';
}