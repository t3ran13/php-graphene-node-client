<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class GetTrendingCategoriesCommand extends CommandAbstract
{
    protected $method = 'get_trending_categories';
    protected $requiredParams = [
        0, //'after'
        1, //'limit'
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