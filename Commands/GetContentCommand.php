<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetContentCommand extends CommandAbstract
{
    /** @var string  */
    protected $method = 'get_content';

    /** @var array  */
    protected $requiredParams = [
        0, //'author',
        1 //'permlink'
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