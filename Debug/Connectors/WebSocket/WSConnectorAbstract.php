<?php

namespace GrapheneNodeClient\Debug\Connectors\WebSocket;

use GrapheneNodeClient\Connectors\WebSocket\WSConnectorAbstract AS ParentWSConnectorAbstract;

abstract class WSConnectorAbstract extends ParentWSConnectorAbstract
{
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY)
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
        echo "\n DATA IN REQUEST";
        echo "\n";
        echo print_r($data, true);

        $connection = $this->getConnection();
        $connection->send(json_encode($data));

        $data = $connection->receive();
        $answer = json_decode($data, self::ANSWER_FORMAT_ARRAY === $answerFormat);

        echo "\n DATA IN ANSWER";
        echo "\n";
        echo print_r($answer, true);

        return $answer;
    }
}