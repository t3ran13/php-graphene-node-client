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
    protected static $nodeURL = ['wss://ws.viz.ropox.tools', 'wss://viz.lexai.host', 'wss://solox.world/ws'];
}