<?php


namespace GrapheneNodeClient\Connectors;


use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
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
                self::$connectors[$platform] = new SteemitHttpConnector();
            }
        }

        return self::$connectors[$platform];
    }
}