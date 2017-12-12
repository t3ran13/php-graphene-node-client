<?php

namespace GrapheneNodeClient\Connectors\JsonRPC;


class SteemitJRPCConnector extends JRPCConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_STEEMIT;

    /**
     * https or http server
     *
     * @var string
     */
    protected $nodeURL = 'https://api.steemit.com';
}