<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

/**
 * @property $this get_block
 */
class Commands implements CommandInterface
{
    /** @var string */
    protected $method = '';
    /** @var array */
    protected $queryDataMap = [];
    /** @var ConnectorInterface */
    protected $connector;
    /** @var string */
    private $apiName;

    //protected $projectApi = [ 'method_name' => [ 'apiName' => 'api_name', 'fields'=>['массив с полями из команды']]];
    //protected $steemAPI = [
    protected $golosAPI = [
        'get_block' => [
            'apiName' => 'database_api',
            'fields' => [
                '0' => ['integer'], //block_id
            ]
        ],
        'get_accounts' => [
            'apiName' => 'database_api',
            'fields' => [
                '0' => ['array'], //authors
            ]
        ],
        'get_account_count' => [
            'apiName' => 'database_api',
            'fields' => []
        ],
        'get_account_history' => [
            'apiName' => 'database_api',
            'fields' => [
                '0' => ['string'], //authors
                '1' => ['integer'], //from
                '2' => ['integer'], //limit max 2000
            ]
        ],
        /*'get_account_count' => [
            'apiName' => 'database_api',
            'fields' => [
                '0' => ['array'], //authors
            ]
        ],*/
    ];

    //protected $projectApi = [ 'method_name' => [ 'apiName' => 'api_name', 'fields'=>['массив с полями из команды']]];


    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * @param CommandQueryDataInterface $commandQueryData
     * @param string $answerFormat
     * @param string $getElementWithKey If you want to get only certain element from answer.
     *                                  Example: 'key:123:qwe' => $array['key']['123']['qwe'] or $object->key->123->qwe
     * @return array|object
     */
    public function execute(
        CommandQueryDataInterface $commandQueryData,
        $getElementWithKey = null,
        $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY
    ) {
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

    public function __get($name)
    {
        $platform = $this->connector->getPlatform();

        if ($platform===ConnectorInterface::PLATFORM_GOLOS){
            $api = $this->golosAPI;
        }

        if ($platform===ConnectorInterface::PLATFORM_STEEMIT){
            $api = $this->steemAPI;
        }

        if (!isset($api)){
            throw new \Exception('There is no api');
        }

        if (!isset($api[$name])){
            throw new \Exception('There is no information about command:'.$name. '. Please create your own class for that command');
        }


        $this->apiName = $api[$name]['apiName'];
        $this->queryDataMap = $api[$name]['fields'];
        $this->method = $name;
        return $this;
    }
}