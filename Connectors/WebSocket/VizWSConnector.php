<?php

namespace GrapheneNodeClient\Connectors\WebSocket;


class VizWSConnector extends WSConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_VIZ;

    /**
     * wss or ws server
     *
     * @var string
     */
    protected static $nodeURL = ['wss://api.viz.blckchnd.com/ws', 'wss://ws.viz.ropox.tools'];
}