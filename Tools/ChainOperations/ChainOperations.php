<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


class ChainOperations
{
    const OPERATION_VOTE        = 'vote';
    const OPERATION_COMMENT     = 'comment';
    const OPERATION_TRANSFER    = 'transfer';
    const OPERATION_CUSTOM_JSON = 'custom_json';

    const OPERATIONS_IDS = [
        self::OPERATION_VOTE        => 0,
        self::OPERATION_COMMENT     => 1,
        self::OPERATION_TRANSFER    => 2,
        self::OPERATION_CUSTOM_JSON => 18
    ];

    /**
     * @param string $operationName
     *
     * @return integer
     */
    public static function getOperationId($operationName)
    {
        return self::OPERATIONS_IDS[$operationName];
    }
}