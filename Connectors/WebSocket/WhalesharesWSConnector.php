<?php

namespace GrapheneNodeClient\Connectors\WebSocket;


class WhalesharesWSConnector extends WSConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_GOLOS;

    /**
     * wss or ws server
     *
     * @var string
     */
    protected static $nodeURL = [
        'wss://whaleshares.io/ws',
        'ws://188.166.99.136:8090',
        'ws://rpc.kennybll.com:8090'
    ];
}