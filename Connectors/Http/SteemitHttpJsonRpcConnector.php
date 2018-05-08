<?php

namespace GrapheneNodeClient\Connectors\Http;



class SteemitHttpJsonRpcConnector extends HttpJsonRpcConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_STEEMIT;

    /**
     * https or http server
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected static $nodeURL = ['https://steemd.privex.io', 'https://rpc.steemviz.com', 'https://api.steemit.com', 'https://rpc.buildteam.io', 'https://steemd.pevo.science', 'https://steemd.minnowsupportproject.org'];
}