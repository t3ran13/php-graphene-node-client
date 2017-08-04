<?php


namespace GrapheneNodeClient\Commands\Follow;


use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\CommandQueryDataInterface;
use GrapheneNodeClient\Connectors\ConnectorInterface;

abstract class CommandAbstract implements CommandInterface
{
    /** @var string */
    protected $method = '';
    /** @var array */
    protected $queryDataMap = [];
    /** @var ConnectorInterface */
    protected $connector;
    /** @var string */
    protected $apiName = 'follow_api';


    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @param CommandQueryDataInterface $commandQueryData
     * @param string $answerFormat
     * @param string $getElementWithKey If you want to get only certain element from answer.
     *                                  Example: 'key:123:qwe' => $array['key']['123']['qwe'] or $object->key->123->qwe
     * @return array|object
     */
    public function execute(CommandQueryDataInterface $commandQueryData, $getElementWithKey = null, $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        /** @var CommandQueryData $commandQueryData */
        $params = $commandQueryData->prepareData($this->getQueryDataMap());

        $answer = $this->doRequest($params, $answerFormat);

        $defaultValue = $answerFormat === ConnectorInterface::ANSWER_FORMAT_ARRAY ? [] : ((object)[]);

        return $this->getElementByKey($answer, $getElementWithKey, $defaultValue);
    }


    /**
     * @return array|mixed
     */
    public function getQueryDataMap()
    {
        return isset($this->queryDataMap[$this->connector->getPlatform()])
            ? $this->queryDataMap[$this->connector->getPlatform()]
            : $this->queryDataMap;
    }

    /**
     * @param array $params
     * @param string $answerFormat
     * @return array|object
     */
    protected function doRequest($params, $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY)
    {
        $data = [
            'method' => $this->method,
            'params' => $params
        ];

        return $this->connector->doRequest($this->apiName, $data, $answerFormat);
    }


    /**
     * get all values or vulue by key
     *
     * Example: 'key:123:qwe' => $array['key']['123']['qwe'] or $object->key->123->qwe
     *
     * @param null|string $getKey
     * @param null|mixed $default
     * @param array|object $array
     *
     * @return mixed
     */
    protected function getElementByKey($array, $getKey = null, $default = null)
    {
        $data = $array;
        if ($getKey) {
            $keyParts = explode(':', $getKey);
            foreach ($keyParts as $key) {
                if (is_array($data) && isset($data[$key])) {
                    $data = $data[$key];
                } elseif (is_object($data) && isset($data->$key)) {
                    $data = $data->$key;
                } else {
                    $data = null;
                    break;
                }
            }
        }

        if ($data === null) {
            $data = $default;
        }

        return $data;
    }
}