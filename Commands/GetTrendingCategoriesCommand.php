<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetTrendingCategoriesCommand extends CommandAbstract
{
    protected $method = 'get_trending_categories';
    protected $requiredParams = [
        0, //'after'
        1, //'limit'
    ];
}