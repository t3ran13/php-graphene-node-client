<?php


namespace GrapheneNodeClient\Commands\Login;


class GetApiByNameCommand extends CommandAbstract
{
    protected $method       = 'get_api_by_name';
    protected $queryDataMap = [
        '0' => ['string'], //'api_name',for example follow_api, database_api, login_api and ect.
    ];
}