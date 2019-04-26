<?php


namespace GrapheneNodeClient\Commands;


class SteemitApiMethods
{
    /**
     *  [ 'method_name' => [ 'apiName' => 'api_name', 'fields'=>['массив с полями из команды']]];
     *
     * @var array
     */
    public static $map = [
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
        'get_discussions_by_comments'               => [
            'apiName' => 'database_api',
            'fields'  => [
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
        'get_feed_history'         => [
            'apiName' => 'witness_api',
            'fields'  => [
            ]
        ],
        'get_follow_count'                         => [
            'apiName' => 'follow_api',
            'fields'  => [
                '0' => ['string'], //author
            ]
        ],
        'get_ops_in_block'                      => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['integer'], //blockNum
                '1' => ['bool'], //onlyVirtual
            ]
        ],
        'get_reward_fund'                          => [
            'apiName' => 'database_api',
            'fields'  => [
                '0' => ['string'], //post or comments
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
        'get_transaction_hex'                 => [
            'apiName' => 'database_api',
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
}