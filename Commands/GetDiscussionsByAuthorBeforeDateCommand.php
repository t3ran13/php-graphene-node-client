<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetDiscussionsByAuthorBeforeDateCommand extends CommandAbstract
{
    protected $method = 'get_discussions_by_author_before_date';
    protected $requiredParams = [
        0, //'author',
        1, //'start_permlink',
        2, //'before_date',
        3 //'limit'
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