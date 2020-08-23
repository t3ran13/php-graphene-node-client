<?php

namespace GrapheneNodeClient\Connectors\Http;


class HiveHttpJsonRpcConnector extends HttpJsonRpcConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_HIVE;

    /**
     * https or http server
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected static $nodeURL = [
        'https://steemd.privex.io',
        'https://api.steemit.com',
        'https://anyx.io',
        'https://rpc.usesteem.com'
//        'https://steemd.pevo.science' //too often 503
//        'https://steemd.minnowsupportproject.org' //not full answers, some fields are empty
    ];
}