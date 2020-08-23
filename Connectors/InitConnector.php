<?php


namespace GrapheneNodeClient\Connectors;


use GrapheneNodeClient\Connectors\Http\SteemitHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\Http\HiveHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\Http\VizHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\VizWSConnector;

class InitConnector
{
    /**
     * @var ConnectorInterface
     */
    protected static $connectors = [];
    protected static $platforms = [
        ConnectorInterface::PLATFORM_VIZ,
        ConnectorInterface::PLATFORM_GOLOS,
        ConnectorInterface::PLATFORM_STEEMIT,
        ConnectorInterface::PLATFORM_HIVE,
        ConnectorInterface::PLATFORM_WHALESHARES
    ];

    public static function getConnector($platform)
    {
        if (!in_array($platform, self::$platforms)) {
            throw new \Exception('Wrong platform');
        }
        if (!isset(self::$connectors[$platform])) {
            if ($platform === ConnectorInterface::PLATFORM_VIZ) {
                self::$connectors[$platform] = new VizWSConnector();
            } elseif ($platform === ConnectorInterface::PLATFORM_GOLOS) {
                self::$connectors[$platform] = new GolosWSConnector();
            } elseif ($platform === ConnectorInterface::PLATFORM_STEEMIT) {
                self::$connectors[$platform] = new SteemitHttpJsonRpcConnector();
            } elseif ($platform === ConnectorInterface::PLATFORM_HIVE) {
                self::$connectors[$platform] = new HiveHttpJsonRpcConnector();
            }
        }

        return self::$connectors[$platform];
    }
}