<?php


namespace GrapheneNodeClient\Connectors;


use GrapheneNodeClient\Connectors\Http\SteemitHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;

class InitConnector
{
    /**
     * @var ConnectorInterface
     */
    protected static $connectors = [];
    protected static $platforms = [
        ConnectorInterface::PLATFORM_GOLOS,
        ConnectorInterface::PLATFORM_STEEMIT
    ];

    public static function getConnector($platform)
    {
        if (!in_array($platform, self::$platforms)) {
            throw new \Exception('Wrong platform');
        }
        if (!isset(self::$connectors[$platform])) {
            if ($platform === ConnectorInterface::PLATFORM_GOLOS) {
                self::$connectors[$platform] = new GolosWSConnector();
            } elseif ($platform === ConnectorInterface::PLATFORM_STEEMIT) {
                self::$connectors[$platform] = new SteemitHttpJsonRpcConnector();
            }
        }

        return self::$connectors[$platform];
    }
}