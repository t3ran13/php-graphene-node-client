<?php


namespace GrapheneNodeClient\Commands;

interface CommandInterface
{
    /**
     * @param array $params
     * @return mixed
     */
    public function execute($params = []);

    /**
     * @return array
     */
    public function getDataMap();
}