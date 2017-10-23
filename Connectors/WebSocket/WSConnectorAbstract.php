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

    /**
     * max number of tries to get answer from API
     *
     * @var int
     */
    protected $maxNumberOfTriesToCallApi = 3;

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

    /**
     * @param string $apiName
     * @param array  $data
     * @param string $answerFormat
     * @param int $try_number Try number of getting answer from api
     *
     * @return array|object
     * @throws ConnectionException
     * @throws \WebSocket\BadOpcodeException
     */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1)
    {
        $data = [
            'id'     => $this->getNextId(),
            'method' => 'call',
            'params' => [
                $apiName,
                $data['method'],
                $data['params']
            ]
        ];
        try {
            $connection = $this->getConnection();
            $connection->send(json_encode($data));

            $data = $connection->receive();
            $answer = json_decode($data, self::ANSWER_FORMAT_ARRAY === $answerFormat);
        } catch (ConnectionException $e) {
            //if got WS Exception, try to get answer again
            if ($try_number < $this->maxNumberOfTriesToCallApi) {
                $answer = $this->doRequest($apiName, $data, $answerFormat, $try_number + 1);
            } else {
                throw $e;
            }
        }

        return $answer;
    }
}