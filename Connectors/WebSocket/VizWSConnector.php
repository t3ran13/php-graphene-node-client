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
    protected static $nodeURL = ['wss://viz.lexa.host/ws', 'wss://solox.world/ws', 'wss://viz-node.dpos.space/ws'];
}