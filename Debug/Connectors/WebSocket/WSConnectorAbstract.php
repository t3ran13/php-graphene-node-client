<?php

namespace GrapheneNodeClient\Debug\Connectors\WebSocket;

use GrapheneNodeClient\Connectors\WebSocket\WSConnectorAbstract AS ParentWSConnectorAbstract;
use WebSocket\ConnectionException;

abstract class WSConnectorAbstract extends ParentWSConnectorAbstract
{
    protected $wsTimeoutSeconds = 5;
    protected $maxNumberOfTriesToCallApi = 5;

    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY, $try_number = 1)
    {
        $requestId = $this->getNextId();
        $data = [
            'id'     => $requestId,
            'method' => 'call',
            'params' => [
                $apiName,
                $data['method'],
                $data['params']
            ]
        ];
        echo "\n DATA IN REQUEST: try {$try_number}, requestId {$requestId}";
        echo "\n";
//        echo print_r($data, true);

        try {
            $connection = $this->getConnection();
            $connection->send(json_encode($data));

            $answerRaw = $connection->receive();
            $answer = json_decode($answerRaw, self::ANSWER_FORMAT_ARRAY === $answerFormat);

//            echo "\n DATA IN ANSWER";
//            echo "\n";
//            echo print_r($answer, true);

            //check that answer has the same id or id from previous tries, else it is answer from other request
            if (self::ANSWER_FORMAT_ARRAY === $answerFormat) {
                $answerId = $answer['id'];
            } elseif (self::ANSWER_FORMAT_OBJECT === $answerFormat) {
                $answerId = $answer->id;
            }
            echo "\n DATA IN ANSWER: try {$try_number}, answerId {$answerId}";
            if ($requestId - $answerId > ($try_number - 1)) {
                throw new ConnectionException('get answer from old request');
            }


        } catch (ConnectionException $e) {
            echo "\n cautch ConnectionException";
            //if got WS Exception, try to get answer again
            if ($try_number < $this->maxNumberOfTriesToCallApi) {
                $answer = $this->doRequest($apiName, $data, $answerFormat, $try_number + 1);
            } elseif ($this->isExistReserveNodeUrl()) {
                echo "\n connectToReserveNode";
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