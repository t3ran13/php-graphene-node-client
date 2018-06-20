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
 * @method Commands get_api_by_name() ONLY for STEEM
 * @method Commands get_block()
 * @method Commands get_block_header()
 * @method Commands get_content()
 * @method Commands get_content_replies()
 * @method Commands get_current_median_history_price()
 * @method Commands get_discussions_by_author_before_date()
 * @method Commands get_discussions_by_blog()
 * @method Commands get_discussions_by_created()
 * @method Commands get_discussions_by_feed()
 * @method Commands get_discussions_by_trending()
 * @method Commands get_dynamic_global_properties()
 * @method Commands get_followers()
 * @method Commands get_ops_in_block()
 * @method Commands get_trending_categories() //only steem
 * @method Commands get_version()
 * @method Commands get_witnesses_by_vote()
 * @method Commands login() ONLY for STEEM
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
    protected $steemAPI = [
        'get_block'                             => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['integer'], //block_id
            ]
        ],
        'get_accounts'                          => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['array'], //authors
            ]
        ],
        'get_account_count'                     => [
            'apiName' => 'database_api',
            'fields'  => []
        ],
        'get_account_history'                   => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //authors
                '1' => ['integer'], //from
                '2' => ['integer'], //limit max 2000
            ]
        ],
        'get_account_votes'                     => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //account name
            ]
        ],
        'get_active_votes'                      => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['string'], //permlink
            ]
        ],
        'get_active_witnesses'                  => [
            'apiName' => 'database_api',
            'fields'  => [
            ]
        ],
        'get_content'                           => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['string'], //permlink
            ]
        ],
        'get_block_header'                      => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['integer'], //block_id
            ]
        ],
        'get_config'                   => [
            'apiName' => 'database_api',
            'fields'  => [
            ]
        ],
        'get_content_replies'                   => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['string'], //permlink
            ]
        ],
        'get_current_median_history_price'      => [
            'apiName' => 'database_api',
            'fields'  => [
            ]
        ],
        'get_discussions_by_author_before_date' => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //'author',
                '1' => ['string'], //'start_permlink' for pagination,
                '2' => ['string'], //'before_date'
                '3' => ['integer'], //'limit'
            ]
        ],
        'get_discussions_by_blog'               => [
            'apiName' => 'database_api',
            'fields'  => [
                '*:tag'            => ['string'], //'author',
                '*:limit'          => ['integer'], //'limit'
                '*:start_author'   => ['nullOrString'], //'start_author' for pagination,
                '*:start_permlink' => ['nullOrString'] //'start_permlink' for pagination,
            ]
        ],
        'get_discussions_by_created'            => [
            'apiName' => 'database_api',
            'fields'  => [
                '*:tag'            => ['nullOrString'], //'author',
                '*:limit'          => ['integer'], //'limit'
                '*:start_author'   => ['nullOrString'], //'start_author' for pagination,
                '*:start_permlink' => ['nullOrString'] //'start_permlink' for pagination,
            ]
        ],
        'get_discussions_by_feed'               => [
            'apiName' => 'database_api',
            'fields'  => [
                '*:tag'            => ['string'], //'author',
                '*:limit'          => ['integer'], //'limit'
                '*:start_author'   => ['nullOrString'], //'start_author' for pagination,
                '*:start_permlink' => ['nullOrString'] //'start_permlink' for pagination,
            ]
        ],
        'get_discussions_by_trending'           => [
            'apiName' => 'database_api',
            'fields'  => [
                '*:tag'            => ['nullOrString'], //'author',
                '*:limit'          => ['integer'], //'limit'
                '*:start_author'   => ['nullOrString'], //'start_author' for pagination,
                '*:start_permlink' => ['nullOrString'] //'start_permlink' for pagination,
            ]
        ],
        'get_dynamic_global_properties'         => [
            'apiName' => 'database_api',
            'fields'  => [
            ]
        ],
        'get_ops_in_block'                      => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['integer'], //blockNum
                '1' => ['bool'], //onlyVirtual
            ]
        ],
        'get_trending_categories'               => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['nullOrString'], //after
                '1' => ['integer'], //permlink
            ]
        ],
        'get_trending_tags'               => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['nullOrString'], //after
                '1' => ['integer'], //permlink
            ]
        ],
        'get_witnesses_by_vote'                 => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //from accountName, can be empty string ''
                '1' => ['integer'] //limit
            ]
        ],
        'get_followers'                         => [
            'apiName' => 'follow_api',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['nullOrString'], //startFollower
                '2' => ['string'], //followType //blog, ignore
                '3' => ['integer'], //limit
            ]
        ],
        'login'                                 => [
            'apiName' => 'login_api',
            'fields'  => [
                0 => ['string'],
                1 => ['string']
            ]
        ],
        'get_version'                           => [
            'apiName' => 'login_api',
            'fields'  => [
            ]
        ],
        'get_api_by_name'                       => [
            'apiName' => 'login_api',
            'fields'  => [
                '0' => ['string'], //'api_name',for example follow_api, database_api, login_api and ect.
            ]
        ],
        'broadcast_transaction'                 => [
            'apiName' => 'network_broadcast_api',
            'fields'  => [
                '0:ref_block_num'    => ['integer'],
                '0:ref_block_prefix' => ['integer'],
                '0:expiration'       => ['string'],
                '0:operations:*:0'   => ['string'],
                '0:operations:*:1'   => ['array'],
                '0:extensions'       => ['array'],
                '0:signatures'       => ['array']
            ]
        ],
        'broadcast_transaction_synchronous'     => [
            'apiName' => 'network_broadcast_api',
            'fields'  => [
                '0:ref_block_num'    => ['integer'],
                '0:ref_block_prefix' => ['integer'],
                '0:expiration'       => ['string'],
                '0:operations:*:0'   => ['string'],
                '0:operations:*:1'   => ['array'],
                '0:extensions'       => ['array'],
                '0:signatures'       => ['array']
            ]
        ],
    ];
    protected $golosAPI = [
        'get_block'                             => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['integer'], //block_id
            ]
        ],
        'get_accounts'                          => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['array'], //authors
            ]
        ],
        'get_account_count'                     => [
            'apiName' => 'database_api',
            'fields'  => []
        ],
        'get_account_history'                   => [
            'apiName' => 'account_history',
            'fields'  => [
                '0' => ['string'], //authors
                '1' => ['integer'], //from
                '2' => ['integer'], //limit max 2000
            ]
        ],
        'get_account_votes'                     => [
            'apiName' => 'social_network',
            'fields'  => [
                '0' => ['string'], //account name
                '1' => ['nullOrInteger'] //voteLimit by default 10 000
            ]
        ],
        'get_active_votes'                      => [
            'apiName' => 'social_network',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['string'], //permlink
                '2' => ['nullOrInteger'] //voteLimit by default 10 000
            ]
        ],
        'get_active_witnesses'                  => [
            'apiName' => 'witness_api',
            'fields'  => [
            ]
        ],
        'get_block_header'                      => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['integer'], //block_id
            ]
        ],
        'get_config'                   => [
            'apiName' => 'database_api',
            'fields'  => [
            ]
        ],
        'get_content'                           => [
            'apiName' => 'social_network',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['string'], //permlink
                '2' => ['nullOrInteger'] //voteLimit by default 10 000
            ]
        ],
        'get_content_replies'                   => [
            'apiName' => 'social_network',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['string'], //permlink
                '2' => ['nullOrInteger'] //voteLimit by default 10 000
            ]
        ],
        'get_current_median_history_price'      => [
            'apiName' => 'witness_api',
            'fields'  => [
            ]
        ],
        'get_discussions_by_author_before_date' => [
            'apiName' => 'tags',
            'fields'  => [
                '0' => ['string'], //'author',
                '1' => ['string'], //'start_permlink' for pagination,
                '2' => ['string'], //'before_date'
                '3' => ['integer'], //'limit'
                '4' => ['nullOrInteger'] //voteLimit by default 10 000
            ]
        ],
        'get_discussions_by_blog'               => [
            'apiName' => 'tags',
            'fields'  => [
                '*:limit'            => ['integer'], //the discussions return amount top limit
                '*:select_tags:*'    => ['nullOrString'], //list of tags to include, posts without these tags are filtered
                '*:select_authors:*' => ['nullOrString'], //list of authors to select
                '*:truncate_body'    => ['nullOrInteger'], //the amount of bytes of the post body to return, 0 for all
                '*:start_author'     => ['nullOrString'], //the author of discussion to start searching from
                '*:start_permlink'   => ['nullOrString'], //the permlink of discussion to start searching from
                '*:parent_author'    => ['nullOrString'], //the author of parent discussion
                '*:parent_permlink'  => ['nullOrString'] //the permlink of parent discussion
            ]
        ],
        'get_discussions_by_created'            => [
            'apiName' => 'tags',
            'fields'  => [
                '*:limit'            => ['integer'], //the discussions return amount top limit
                '*:select_tags:*'    => ['nullOrString'], //list of tags to include, posts without these tags are filtered
                '*:select_authors:*' => ['nullOrString'], //list of authors to select
                '*:truncate_body'    => ['nullOrInteger'], //the amount of bytes of the post body to return, 0 for all
                '*:start_author'     => ['nullOrString'], //the author of discussion to start searching from
                '*:start_permlink'   => ['nullOrString'], //the permlink of discussion to start searching from
                '*:parent_author'    => ['nullOrString'], //the author of parent discussion
                '*:parent_permlink'  => ['nullOrString'] //the permlink of parent discussion
            ],
        ],
        'get_discussions_by_feed'               => [
            'apiName' => 'tags',
            'fields'  => [
                '*:limit'            => ['integer'], //the discussions return amount top limit
                '*:select_tags:*'    => ['nullOrString'], //list of tags to include, posts without these tags are filtered
                '*:select_authors:*' => ['nullOrString'], //list of authors to select
                '*:truncate_body'    => ['nullOrInteger'], //the amount of bytes of the post body to return, 0 for all
                '*:start_author'     => ['nullOrString'], //the author of discussion to start searching from
                '*:start_permlink'   => ['nullOrString'], //the permlink of discussion to start searching from
                '*:parent_author'    => ['nullOrString'], //the author of parent discussion
                '*:parent_permlink'  => ['nullOrString'] //the permlink of parent discussion
            ]
        ],
        'get_discussions_by_trending'           => [
            'apiName' => 'tags',
            'fields'  => [
                '*:limit'            => ['integer'], //the discussions return amount top limit
                '*:select_tags:*'    => ['nullOrString'], //list of tags to include, posts without these tags are filtered
                '*:select_authors:*' => ['nullOrString'], //list of authors to select
                '*:truncate_body'    => ['nullOrInteger'], //the amount of bytes of the post body to return, 0 for all
                '*:start_author'     => ['nullOrString'], //the author of discussion to start searching from
                '*:start_permlink'   => ['nullOrString'], //the permlink of discussion to start searching from
                '*:parent_author'    => ['nullOrString'], //the author of parent discussion
                '*:parent_permlink'  => ['nullOrString'] //the permlink of parent discussion
            ]
        ],
        'get_dynamic_global_properties'         => [
            'apiName' => 'database_api',
            'fields'  => [
            ]
        ],
        'get_ops_in_block'                      => [
            'apiName' => 'operation_history',
            'fields'  => [
                '0' => ['integer'], //blockNum
                '1' => ['bool'], //onlyVirtual
            ]
        ],
        'get_trending_tags'               => [
            'apiName' => 'tags',
            'fields'  => [
                '0' => ['nullOrString'], //after
                '1' => ['integer'], //permlink
            ]
        ],
        'get_witnesses_by_vote'                 => [
            'apiName' => 'witness_api',
            'fields'  => [
                '0' => ['string'], //from accountName, can be empty string ''
                '1' => ['integer'] //limit
            ]
        ],
        'get_followers'                         => [
            'apiName' => 'follow',
            'fields'  => [
                '0' => ['string'], //author
                '1' => ['nullOrString'], //startFollower
                '2' => ['string'], //followType //blog, ignore
                '3' => ['integer'], //limit
            ]
        ],
        'get_version'                           => [
            'apiName' => 'login_api',
            'fields'  => [
            ]
        ],
        'broadcast_transaction'                 => [
            'apiName' => 'network_broadcast_api',
            'fields'  => [
                '0:ref_block_num'    => ['integer'],
                '0:ref_block_prefix' => ['integer'],
                '0:expiration'       => ['string'],
                '0:operations:*:0'   => ['string'],
                '0:operations:*:1'   => ['array'],
                '0:extensions'       => ['array'],
                '0:signatures'       => ['array']
            ]
        ],
        'broadcast_transaction_synchronous'     => [
            'apiName' => 'network_broadcast_api',
            'fields'  => [
                '0:ref_block_num'    => ['integer'],
                '0:ref_block_prefix' => ['integer'],
                '0:expiration'       => ['string'],
                '0:operations:*:0'   => ['string'],
                '0:operations:*:1'   => ['array'],
                '0:extensions'       => ['array'],
                '0:signatures'       => ['array']
            ]
        ],
    ];

    //protected $projectApi = [ 'method_name' => [ 'apiName' => 'api_name', 'fields'=>['массив с полями из команды']]];


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
        return isset($this->queryDataMap[$this->connector->getPlatform()])
            ? $this->queryDataMap[$this->connector->getPlatform()]
            : $this->queryDataMap;
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

        if ($platform === ConnectorInterface::PLATFORM_GOLOS) {
            $api = $this->golosAPI;
        } elseif ($platform === ConnectorInterface::PLATFORM_STEEMIT) {
            $api = $this->steemAPI;
        }

        if (!isset($api)) {
            throw new \Exception('There is no api');
        }

        if (!isset($api[$name])) {
            throw new \Exception('There is no information about command:' . $name . '. Please create your own class for that command');
        }


        $this->apiName = $api[$name]['apiName'];
        $this->queryDataMap = $api[$name]['fields'];
        $this->method = $name;

        return $this;
    }
}