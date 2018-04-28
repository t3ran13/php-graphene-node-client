<?php

namespace GrapheneNodeClient\Connectors\Http;



class SteemitHttpConnector extends HttpConnectorAbstract
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
    protected $nodeURL = ['https://steemd.privex.io','api.steemit.com'];
}