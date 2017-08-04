<?php


namespace GrapheneNodeClient\Commands\DataBase;


class GetDynamicGlobalPropertiesCommand extends CommandAbstract
{
    /** @var string */
    protected $method = 'get_dynamic_global_properties';

    /** @var array */
    protected $queryDataMap = [];
}