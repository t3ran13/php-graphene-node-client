<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class ChainOperationsViz
{
    const OP_IDS = [
        ChainOperations::OPERATION_VOTE        => 0,
        ChainOperations::OPERATION_COMMENT     => 1,
        ChainOperations::OPERATION_TRANSFER    => 2,
        ChainOperations::OPERATION_CUSTOM_JSON => 18,
    ];
}