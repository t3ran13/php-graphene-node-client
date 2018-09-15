<?php

namespace GrapheneNodeClient\Connectors\WebSocket;

use GrapheneNodeClient\Connectors\ConnectorInterface;
use WebSocket\Client;
use WebSocket\ConnectionException;

abstract class WSConnectorAbstract implements ConnectorInterface
{
    /**
     * @var string
     */
    protected $platform;

    /**
     * wss or ws servers, can be list. First node is default, other are reserve.
     * After $maxNumberOfTriesToCallApi tries connects to default it is connected to reserve node.
     *
     * @var string|array
     */
    protected static $nodeURL;

    /**
     * current node url, for example 'wss://ws.golos.io'
     *
     * @var string
     */
    private $reserveNodeUrlList;


    /**
     * current node url, for example 'wss://ws.golos.io'
     *
     * if you set several nodes urls, if with first node will be trouble
     * it will connect after $maxNumberOfTriesToCallApi tries to next node
     *
     * @var string
     */
    protected static $currentNodeURL;

    /**
     * waiting answer from Node during $wsTimeoutSeconds seconds
     *
     * @var int
     */
    protected $wsTimeoutSeconds = 5;

    /**
     * max number of tries to get answer from the node
     *
     * @var int
     */
    protected $maxNumberOfTriesToCallApi = 3;

    protected static $connection;
    protected static $currentId;

    /**
     * WSConnectorAbstract constructor.
     *
     * @param int $orderNodesByTimeout skip this checks when it is 0.
     *                                   do not set it is too low, if node do not answer it go out from list
     *
     * @throws \WebSocket\BadOpcodeException
     */
    public function __construct($orderNodesByTimeout = 0) {

        if ($orderNodesByTimeout > 0 && is_array(static::$nodeURL) && count(static::$nodeURL) > 1) {
            $this->orderNodesByTimeout($orderNodesByTimeout);
        }
    }

    /**
     * @param int $timeoutSeconds
     */
    public function setConnectionTimeoutSeconds($timeoutSeconds)
    {
        $this->wsTimeoutSeconds = $timeoutSeconds;
    }

    /**
     * Number of tries to reconnect (get correct answer) to api
     *
     * @param int $triesN
     */
    public function setMaxNumberOfTriesToReconnect($triesN)
    {
        $this->maxNumberOfTriesToCallApi = $triesN;
    }


    /**
     * @param integer $orderNodesByTimeout Only if you set few nodes. do not set it is too low, if node do not answer it go out from list
     *
     *
     * @throws \WebSocket\BadOpcodeException
     */
    public function orderNodesByTimeout($orderNodesByTimeout)
    {
        $requestId = $this->getNextId();
        $limits = [4, 7];
        $requestData = [
            'jsonrpc' => '2.0',
            'id'      => $requestId,
            'method'  => 'call',
            'params'  => [
                'database_api',
                'get_discussions_by_created',
                [['limit' => 7]]
            ]
        ];
        $wsTimeoutSeconds = $this->wsTimeoutSeconds;
        $this->wsTimeoutSeconds = $orderNodesByTimeout;
        $timeouts = [];
        foreach (static::$nodeURL as $currentNodeURL) {
            try {
                $connection = $this->newConnection($currentNodeURL);

                $startMTime = microtime(true);
                foreach ($limits as $limit) {
                    $requestData['params'][2] = [['limit' => $limit]];

                    $connection->send(json_encode($requestData, JSON_UNESCAPED_UNICODE));
                    $answerRaw = $connection->receive();

                    $answer = json_decode($answerRaw, self::ANSWER_FORMAT_ARRAY);

                    if (isset($answer['error'])) {
                        throw new ConnectionException('got error in answer: ' . $answer['error']['code'] . ' ' . $answer['error']['message']);
                    }
                }
                $timeout = $requestTimeout = microtime(true) - $startMTime;

                if ($connection->isConnected()) {
                    $connection->close();
                }
                $timeouts[$currentNodeURL] = round($timeout, 4);
            } catch (ConnectionException $e) {
            }
        }
        static::$connection = null;
        $this->wsTimeoutSeconds = $wsTimeoutSeconds;
        asort($timeouts);
        static::$nodeURL = array_keys($timeouts);
    }

    /**
     * @return Client|null
     */
    public function getConnection()
    {
        if (self::$connection === null) {
            $this->newConnection($this->getCurrentUrl());
        }

        return self::$connection;
    }

    /**
     * @param string $nodeUrl
     *
     * @return Client
     */
    public function newConnection($nodeUrl)
    {
        self::$connection = new Client($nodeUrl, ['timeout' => $this->wsTimeoutSeconds]);

        return self::$connection;
    }

    public function getCurrentUrl()
    {
        if (
            !isset(static::$currentNodeURL[$this->getPlatform()])
            || static::$currentNodeURL[$this->getPlatform()] === null
            || !in_array(static::$currentNodeURL[$this->getPlatform()], static::$nodeURL)
        ) {
            if (is_array(static::$nodeURL)) {
                $this->reserveNodeUrlList = static::$nodeURL;
                $url = array_shift($this->reserveNodeUrlList);
            } else {
                $url = static::$nodeURL;
            }

            static::$currentNodeURL[$this->getPlatform()] = $url;
        }

        return static::$currentNodeURL[$this->getPlatform()];
    }

    public function isExistReserveNodeUrl()
    {
        return !empty($this->reserveNodeUrlList);
    }

    protected function setReserveNodeUrlToCurrentUrl()
    {
        static::$currentNodeURL[$this->getPlatform()] = array_shift($this->reserveNodeUrlList);
    }

    public function connectToReserveNode()
    {
        $connection = $this->getConnection();
        if ($connection->isConnected()) {
            $connection->close();
        }
        $this->setReserveNodeUrlToCurrentUrl();
        return $this->newConnection($this->getCurrentUrl());
    }

    public function getCurrentId()
    {
        if (self::$currentId === null) {
            self::$currentId = 1;
        }

        return self::$currentId;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function setCurrentId($id)
    {
        self::$currentId = $id;
    }

    public function getNextId()
    {
        $next = $this->getCurrentId() + 1;
        $this->setCurrentId($next);

        return $next;
    }

    /**
     * @param string $apiName
     * @param array  $data
     * @param string $answerFormat
     * @param int    $try_number Try number of getting answer from api
     *
     * @return array|object
     * @throws ConnectionException
     * @throws \WebSocket\BadOpcodeException
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1)
    {
        $requestId = $this->getNextId();
        $requestData = [
            'jsonrpc' => '2.0',
            'id'     => $requestId,
            'method' => 'call',
            'params' => [
                $apiName,
                $data['method'],
                $data['params']
            ]
        ];
        try {
            $connection = $this->getConnection();
            $connection->send(json_encode($requestData, JSON_UNESCAPED_UNICODE));
            $answerRaw = $connection->receive();
            $answer = json_decode($answerRaw, self::ANSWER_FORMAT_ARRAY === $answerFormat);

            if (
                (self::ANSWER_FORMAT_ARRAY === $answerFormat && isset($answer['error']))
                || (self::ANSWER_FORMAT_OBJECT === $answerFormat && isset($answer->error))
            ) {
                throw new ConnectionException('got error in answer: ' . $answer['error']['code'] . ' ' . $answer['error']['message']);
            }
            //check that answer has the same id or id from previous tries, else it is answer from other request
            if (self::ANSWER_FORMAT_ARRAY === $answerFormat) {
                $answerId = $answer['id'];
            } elseif (self::ANSWER_FORMAT_OBJECT === $answerFormat) {
                $answerId = $answer->id;
            }
            if ($requestId - $answerId > ($try_number - 1)) {
                throw new ConnectionException('got answer from old request');
            }


        } catch (ConnectionException $e) {

            if ($try_number < $this->maxNumberOfTriesToCallApi) {
                //if got WS Exception, try to get answer again
                $answer = $this->doRequest($apiName, $data, $answerFormat, $try_number + 1);
            } elseif ($this->isExistReserveNodeUrl()) {
                //if got WS Exception after few ties, connect to reserve node
                $this->connectToReserveNode();
                $answer = $this->doRequest($apiName, $data, $answerFormat);
            } else {
                //if nothing helps
                throw $e;
            }
        }

        return $answer;
    }
}