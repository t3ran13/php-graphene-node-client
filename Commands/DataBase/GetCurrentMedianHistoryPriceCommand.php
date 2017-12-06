<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetCurrentMedianHistoryPriceCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_current_median_history_price';

    /** @var array */
    protected $queryDataMap = [];
}