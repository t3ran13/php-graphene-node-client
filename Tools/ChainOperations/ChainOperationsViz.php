<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class ChainOperationsViz
{
    const IDS = [
        self::OPERATION_TRANSFER    => 2,
        self::OPERATION_CUSTOM      => 10
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
        ]
    ];
}