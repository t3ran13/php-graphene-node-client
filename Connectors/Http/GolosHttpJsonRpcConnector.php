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
    protected static $nodeURL = ['http://only_your_own_node:8090'];
}