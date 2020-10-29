<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class ChainOperationsGolos
{
    const IDS = [
        ChainOperations::OPERATION_VOTE            => 0,
        ChainOperations::OPERATION_COMMENT         => 1,//STEEM/GOLOS/whaleshares
        ChainOperations::OPERATION_COMMENT_OPTIONS => 19,
        ChainOperations::OPERATION_TRANSFER        => 2,
        ChainOperations::OPERATION_CUSTOM_JSON     => 18,
        ChainOperations::OPERATION_WITNESS_UPDATE  => 11,
        ChainOperations::OPERATION_DONATE          => 54,
    ];

    const FIELDS_TYPES = [
        ChainOperations::OPERATION_VOTE            => [
            'voter'    => OperationSerializer::TYPE_STRING,
            'author'   => OperationSerializer::TYPE_STRING,
            'permlink' => OperationSerializer::TYPE_STRING,
            'weight'   => OperationSerializer::TYPE_INT16
        ],
        ChainOperations::OPERATION_COMMENT         => [
            'parent_author'   => OperationSerializer::TYPE_STRING,
            'parent_permlink' => OperationSerializer::TYPE_STRING,
            'author'          => OperationSerializer::TYPE_STRING,
            'permlink'        => OperationSerializer::TYPE_STRING,
            'title'           => OperationSerializer::TYPE_STRING,
            'body'            => OperationSerializer::TYPE_STRING,
            'json_metadata'   => OperationSerializer::TYPE_STRING
        ],
        ChainOperations::OPERATION_COMMENT_OPTIONS => [
            'author'                 => OperationSerializer::TYPE_STRING,
            'permlink'               => OperationSerializer::TYPE_STRING,
            'max_accepted_payout'    => OperationSerializer::TYPE_ASSET,
            'percent_steem_dollars'  => OperationSerializer::TYPE_INT16,
            'allow_votes'            => OperationSerializer::TYPE_BOOL,
            'allow_curation_rewards' => OperationSerializer::TYPE_BOOL,
            'extensions'             => OperationSerializer::TYPE_SET_EXTENSIONS
        ],
        ChainOperations::OPERATION_TRANSFER        => [
            'from'   => OperationSerializer::TYPE_STRING,
            'to'     => OperationSerializer::TYPE_STRING,
            'amount' => OperationSerializer::TYPE_ASSET,
            'memo'   => OperationSerializer::TYPE_STRING
        ],
        ChainOperations::OPERATION_CUSTOM_JSON     => [
            'required_auths'         => OperationSerializer::TYPE_SET_STRING,
            'required_posting_auths' => OperationSerializer::TYPE_SET_STRING,
            'id'                     => OperationSerializer::TYPE_STRING,
            'json'                   => OperationSerializer::TYPE_STRING
        ],
        ChainOperations::OPERATION_WITNESS_UPDATE  => [
            'owner'             => OperationSerializer::TYPE_STRING,
            'url'               => OperationSerializer::TYPE_STRING,
            'block_signing_key' => OperationSerializer::TYPE_PUBLIC_KEY,
            'props'             => OperationSerializer::TYPE_CHAIN_PROPERTIES,
            'fee'               => OperationSerializer::TYPE_ASSET
        ],
        OperationSerializer::TYPE_CHAIN_PROPERTIES => [
            'account_creation_fee' => OperationSerializer::TYPE_ASSET,
            'maximum_block_size'   => OperationSerializer::TYPE_INT32,
            'sbd_interest_rate'    => OperationSerializer::TYPE_INT16
        ],
        ChainOperations::OPERATION_DONATE  => [
            'from'             => OperationSerializer::TYPE_STRING,
            'to'               => OperationSerializer::TYPE_STRING,
            'amount'           => OperationSerializer::TYPE_ASSET,
            'memo'             => OperationSerializer::TYPE_DONATE_MEMO,
            'extensions'       => OperationSerializer::TYPE_SET_FUTURE_EXTENSIONS
        ],
        OperationSerializer::TYPE_DONATE_MEMO => [
            'app'     => OperationSerializer::TYPE_STRING,
            'version' => OperationSerializer::TYPE_INT16,
            'target'  => OperationSerializer::TYPE_VARIANT_OBJECT,
            'comment' => OperationSerializer::TYPE_OPTIONAL_STRING
        ],
        OperationSerializer::TYPE_FUTURE_EXTENSIONS => [
        ]
    ];
}