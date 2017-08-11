<?php


namespace GrapheneNodeClient\Commands\Broadcast;

use GrapheneNodeClient\Commands\CommandQueryDataInterface;

interface CommandInterface
{
    /**
     * @param CommandQueryDataInterface $commandQueryData
     * @return mixed
     */
    public function execute(CommandQueryDataInterface $commandQueryData);

    /**
     * @return array
     */
    public function getQueryDataMap();
}