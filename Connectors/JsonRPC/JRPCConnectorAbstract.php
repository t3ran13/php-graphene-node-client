<?php

namespace GrapheneNodeClient\Connectors\JsonRPC;

use GrapheneNodeClient\Connectors\ConnectorInterface;
use JsonRPC\Client;
use JsonRPC\Exception\ConnectionFailureException;


abstract class JRPCConnectorAbstract implements ConnectorInterface
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
    protected $nodeURL;

    /**
     * current node url, for example 'wss://ws.golos.io'
     *
     * @var string
     */
    private $reserveNodeUrlList;


    /**
     * current node url, for example 'wss://ws.golos.io'
     *
     * @var string
     */
    private $currentNodeURL;

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
        self::$connection = new Client($nodeUrl);

        return self::$connection;
    }

    public function getCurrentUrl()
    {
        if ($this->currentNodeURL === null) {
            if (is_array($this->nodeURL)) {
                $this->reserveNodeUrlList = $this->nodeURL;
                $url = array_shift($this->reserveNodeUrlList);
            } else {
                $url = $this->nodeURL;
            }

            $this->currentNodeURL = $url;
        }

        return $this->currentNodeURL;
    }

    public function isExistReserveNodeUrl()
    {
        return !empty($this->reserveNodeUrlList);
    }

    protected function setReserveNodeUrlToCurrentUrl()
    {
        $this->currentNodeURL = array_shift($this->reserveNodeUrlList);
    }

    public function connectToReserveNode()
    {
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
     * @param array $data
     * @param string $answerFormat
     * @param int $try_number Try number of getting answer from api
     * @return array|object
     * @throws ConnectionFailureException
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1)
    {
        try {
            $connection = $this->getConnection();
            $answer['result'] = $connection->execute($data['method'],($data['params']));

        } catch (ConnectionFailureException $e) {

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