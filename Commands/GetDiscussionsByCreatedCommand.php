<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetDiscussionsByCreatedCommand extends CommandAbstract
{
    protected $method = 'get_discussions_by_created';
    protected $requiredParams = [
        0 => ['tag', 'limit']
    ];

    /**
     * @param array $params
     * @param string $answerFormat
     * @return mixed
     */
    public function execute($params = [], $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        $answer =  parent::execute($params, $answerFormat);

        return $answer['result'];
    }
}