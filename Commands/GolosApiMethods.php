<?php


namespace GrapheneNodeClient\Commands;


class GolosApiMethods
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
}