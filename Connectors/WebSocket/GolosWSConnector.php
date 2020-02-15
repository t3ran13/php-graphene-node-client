<?php

namespace GrapheneNodeClient\Connectors\WebSocket;


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
    protected static $nodeURL = ['wss://api.golos.blckchnd.com/ws', 'wss://golos.lexa.host/ws', 'wss://golos.solox.world/ws', 'wss://api.aleksw.space/ws', 'wss://apinode.golos.today/ws', 'wss://denisgolub.name/ws'];
}