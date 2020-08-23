<?php

namespace GrapheneNodeClient\Connectors\Http;



class GolosHttpJsonRpcConnector extends HttpJsonRpcConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_GOLOS;

    /**
     * https or http server
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected static $nodeURL = ['https://api-full.golos.id', 'https://api-golos.blckchmd.com', 'https://api.aleksw.space', 'https://api-full.golos.id', 'https://apinode.golos.today', 'https://api.golos.blckchnd.com', 'https://golos.solox.world', 'https://golos.lexa.host'];
}