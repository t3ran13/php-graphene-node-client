<?php


namespace GrapheneNodeClient\Commands;


class GetTrendingCategoriesCommand extends CommandAbstract
{
    protected $method = 'get_trending_categories';
    protected $requiredParams = [
        0, //'after'
        1, //'limit'
    ];
}