<?php


namespace GrapheneNodeClient\Connectors;


use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;

class InitConnector
{
    const PLATFORM_GOLOS = 'golos';
    const PLATFORM_STEEMIT = 'steemit';
    /**
     * @var ConnectorInterface
     */
    protected static $connectors = [];
    protected static $platforms = [
        self::PLATFORM_GOLOS,
        self::PLATFORM_STEEMIT
    ];

    public static function getConnector($platform)
    {
        if (!in_array($platform, self::$platforms)) {
            throw new \Exception('Wrong platform');
        }
        if (!isset(self::$connectors[$platform])) {
            if ($platform === self::PLATFORM_GOLOS) {
                self::$connectors[$platform] = new GolosWSConnector();
            } elseif ($platform === self::PLATFORM_STEEMIT) {
                self::$connectors[$platform] = new SteemitWSConnector();
            }
        }

        return self::$connectors[$platform];
    }
}