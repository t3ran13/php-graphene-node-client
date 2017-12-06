<?php

namespace GrapheneNodeClient\Connectors\WebSocket;


class SteemitWSConnector extends WSConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_STEEMIT;

    /**
     * wss or ws server
     *
     * @var string
     */
    protected $nodeURL = 'wss://steemd.steemit.com';
}