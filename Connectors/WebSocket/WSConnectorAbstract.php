<?php

namespace GrapheneNodeClient\Connectors\WebSocket;

use GrapheneNodeClient\Connectors\ConnectorInterface;
use WebSocket\Client;

abstract class WSConnectorAbstract implements ConnectorInterface
{
    /**
     * @var string
     */
    protected $platform;

    /**
     * wss or ws server, for example 'wss://ws.golos.io'
     *
     * @var string
     */
    protected $nodeURL;

    /**
     * waiting answer from Node during $wsTimeoutSeconds seconds
     *
     * @var int
     */
    protected $wsTimeoutSeconds = 5;

    protected static $connection;
    protected static $currentId;

    public function getConnection()
    {
        if (self::$connection === null) {
            self::$connection = new Client($this->nodeURL, ['timeout' => $this->wsTimeoutSeconds]);
        }

        return self::$connection;
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

    public function doRequest(array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY)
    {
        $data = [
            'id'     => $this->getNextId(),
            'method' => $data['method'],
            'params' => $data['params']
        ];
        $connection = $this->getConnection();
        $connection->send(json_encode($data));

        $data = $connection->receive();
        $answer = json_decode($data, self::ANSWER_FORMAT_ARRAY === $answerFormat);

        return $answer;
    }
}