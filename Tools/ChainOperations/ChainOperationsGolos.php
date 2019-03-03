<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class ChainOperationsGolos
{
    const OP_IDS = [
        ChainOperations::OPERATION_VOTE            => 0,
        ChainOperations::OPERATION_COMMENT         => 1,//STEEM/GOLOS/whaleshares
        ChainOperations::OPERATION_COMMENT_OPTIONS => 19,
        ChainOperations::OPERATION_TRANSFER        => 2,
        ChainOperations::OPERATION_CUSTOM_JSON     => 18,
    ];
}