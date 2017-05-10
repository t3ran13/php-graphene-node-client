<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetTrendingCategoriesCommand extends CommandAbstract
{
    protected $method = 'get_trending_categories';
    protected $requiredParams = [
        '0' => ['nullOrString'], //after
        '1' => ['integer'], //permlink
    ];
}