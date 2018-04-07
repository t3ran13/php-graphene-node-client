<?php


namespace GrapheneNodeClient\Debug;

use GrapheneNodeClient\Commands\Single\CommandAbstract;

class TestCommand extends CommandAbstract
{
    protected $method            = 'get_api_by_name';
}