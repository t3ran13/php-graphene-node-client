<?php

namespace GrapheneNodeClient\Debug\Connectors\WebSocket;


class GolosWSConnector extends WSConnectorAbstract
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
    protected $nodeURL = 'wss://ws.golos.io';
}