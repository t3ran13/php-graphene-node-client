<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

/**
 * @method Commands broadcast_transaction()
 * @method Commands broadcast_transaction_synchronous()
 * @method Commands get_accounts()
 * @method Commands get_account_count()
 * @method Commands get_account_history()
 * @method Commands get_account_votes()
 * @method Commands get_active_votes()
 * @method Commands get_active_witnesses()
 * @method Commands get_api_by_name() ONLY for STEEM/whaleshares
 * @method Commands get_block()
 * @method Commands get_block_header()
 * @method Commands get_content()
 * @method Commands get_content_replies()
 * @method Commands get_current_median_history_price() STEEM/GOLOS
 * @method Commands get_discussions_by_author_before_date()
 * @method Commands get_discussions_by_blog()
 * @method Commands get_discussions_by_created()
 * @method Commands get_discussions_by_feed()
 * @method Commands get_discussions_by_trending()
 * @method Commands get_dynamic_global_properties()
 * @method Commands get_followers()
 * @method Commands get_ops_in_block()
 * @method Commands get_trending_categories() //only steem/whaleshares
 * @method Commands get_version()
 * @method Commands get_witnesses_by_vote()
 * @method Commands login() //ONLY for STEEM/whaleshares
 */
class Commands implements CommandInterface
{
    /** @var string */
    protected $method = '';
    /** @var array */
    protected static $queryDataMap = [];
    /** @var ConnectorInterface */
    protected $connector;
    /** @var string */
    private $apiName;


    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * @param CommandQueryDataInterface $commandQueryData
     * @param string                    $answerFormat
     * @param string                    $getElementWithKey If you want to get only certain element from answer.
     *                                                     Example: 'key:123:qwe' => $array['key']['123']['qwe'] or
     *                                                     $object->key->123->qwe
     *
     * @return array|object
     */
    public function execute(
        CommandQueryDataInterface $commandQueryData,
        $getElementWithKey = null,
        $answerFormat = ConnectorInterface::ANSWER_FORMAT_ARRAY
    )
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
        return self::$queryDataMap[$this->connector->getPlatform()][$this->method]['fields'];
    }

    /**
     * @param array  $params
     * @param string $answerFormat
     *
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
     * @param null|string  $getKey
     * @param null|mixed   $default
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

    /**
     * @param $name
     * @param $params
     *
     * @return $this
     * @throws \Exception
     */
    public function __call($name, $params)
    {
        $platform = $this->connector->getPlatform();

        if (!isset(self::$queryDataMap[$platform])) {
            if ($platform === ConnectorInterface::PLATFORM_GOLOS) {
                $api = GolosApiMethods::$map;
            } elseif ($platform === ConnectorInterface::PLATFORM_STEEMIT) {
                $api = SteemitApiMethods::$map;
            } elseif ($platform === ConnectorInterface::PLATFORM_HIVE) {
                $api = HiveApiMethods::$map;
            } elseif ($platform === ConnectorInterface::PLATFORM_VIZ) {
                $api = VizApiMethods::$map;
            } elseif ($platform === ConnectorInterface::PLATFORM_WHALESHARES) {
                $api = WhalesharesApiMethods::$map;
            } else {
                throw new \Exception('There is no api');
            }
            self::$queryDataMap[$platform] = $api;
        }

        if (!isset(self::$queryDataMap[$platform][$name])) {
            throw new \Exception('There is no information about command:' . $name . '. Please create your own class for that command');
        }

        $this->method = $name;
        $this->apiName = self::$queryDataMap[$platform][$this->method]['apiName'];

        return $this;
    }
}