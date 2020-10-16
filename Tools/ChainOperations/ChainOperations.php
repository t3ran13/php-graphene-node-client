<?php

namespace GrapheneNodeClient\Tools\ChainOperations;


use GrapheneNodeClient\Connectors\ConnectorInterface;

class ChainOperations
{
    const OPERATION_VOTE            = 'vote'; //STEEM/GOLOS/whaleshares
    const OPERATION_COMMENT         = 'comment'; //STEEM/GOLOS/whaleshares
    const OPERATION_COMMENT_OPTIONS = 'comment_options'; //STEEM/GOLOS/whaleshares
    const OPERATION_TRANSFER        = 'transfer';
    const OPERATION_CUSTOM_JSON     = 'custom_json';
    const OPERATION_CUSTOM          = 'custom';//only for VIZ
    const OPERATION_WITNESS_UPDATE  = 'witness_update';

    /** @var array */
    protected static $opMap = [];

    /**
     * @param string $chainName
     * @param string $operationName
     *
     * @return integer
     * @throws \Exception
     */
    public static function getOperationId($chainName, $operationName)
    {
        if (!isset(self::$opMap[$chainName])) {
            switch ($chainName) {
                case ConnectorInterface::PLATFORM_GOLOS:
                    $op = ChainOperationsGolos::IDS;
                    break;
                case ConnectorInterface::PLATFORM_HIVE:
                    $op = ChainOperationsHive::IDS;
                    break;
                case ConnectorInterface::PLATFORM_STEEMIT:
                    $op = ChainOperationsSteem::IDS;
                    break;
                case ConnectorInterface::PLATFORM_VIZ:
                    $op = ChainOperationsViz::IDS;
                    break;
                case ConnectorInterface::PLATFORM_WHALESHARES:
                    $op = ChainOperationsWhaleshares::IDS;
                    break;
                default:
                    throw new \Exception("There is no operations id's for '{$chainName}'");
            }

            self::$opMap[$chainName] = $op;
        }

        if (!isset(self::$opMap[$chainName][$operationName])) {
            throw new \Exception("There is no information about operation:'{$operationName}'. Please add ID for this operation");
        }

        return self::$opMap[$chainName][$operationName];
    }
}