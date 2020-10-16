<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class ChainOperationsViz
{
    const IDS = [
        ChainOperations::OPERATION_TRANSFER    => 2,
        ChainOperations::OPERATION_CUSTOM      => 10,
        ChainOperations::OPERATION_WITNESS_UPDATE  => 6,
    ];

    const FIELDS_TYPES = [
        ChainOperations::OPERATION_TRANSFER        => [
            'from'   => OperationSerializer::TYPE_STRING,
            'to'     => OperationSerializer::TYPE_STRING,
            'amount' => OperationSerializer::TYPE_ASSET,
            'memo'   => OperationSerializer::TYPE_STRING
        ],
        ChainOperations::OPERATION_CUSTOM      => [
            'required_auths'         => OperationSerializer::TYPE_SET_STRING,
            'required_posting_auths' => OperationSerializer::TYPE_SET_STRING,
            'id'                     => OperationSerializer::TYPE_STRING,
            'json'                   => OperationSerializer::TYPE_STRING
        ],
        ChainOperations::OPERATION_WITNESS_UPDATE     => [
            'owner'             => OperationSerializer::TYPE_STRING,
            'url'               => OperationSerializer::TYPE_STRING,
            'block_signing_key' => OperationSerializer::TYPE_PUBLIC_KEY
        ]
    ];
}