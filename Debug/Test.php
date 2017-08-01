<?php


namespace GrapheneNodeClient\Debug;


use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\DataBase\GetContentCommand;
use GrapheneNodeClient\Commands\DataBase\GetDiscussionsByAuthorBeforeDateCommand;
use GrapheneNodeClient\Commands\Login\GetApiByNameCommand;
use GrapheneNodeClient\Debug\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Debug\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Debug\TestCommand;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('PATH', __DIR__ . '/..');
require __DIR__ . "/Autoloader.php";
require __DIR__ . '/../vendor/autoload.php';

$command = new GetApiByNameCommand(new SteemitWSConnector());
$commandQueryData = new CommandQueryData();
$commandQueryData->setParams(
    ['follow_api']
);
$command->execute(
    $commandQueryData
);

