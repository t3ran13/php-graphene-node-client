<?php


namespace GrapheneNodeClient\Debug;

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\DataBase\GetActiveWitnessesCommand;
use GrapheneNodeClient\Commands\DataBase\GetBlockCommand;
use GrapheneNodeClient\Commands\DataBase\GetDiscussionsByCreatedCommand;
use GrapheneNodeClient\Commands\DataBase\GetDynamicGlobalPropertiesCommand;
use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionCommand;
use GrapheneNodeClient\Commands\DataBase\GetWitnessesByVoteCommand;
use GrapheneNodeClient\Commands\Login\GetApiByNameCommand;
use GrapheneNodeClient\Commands\Login\GetVersionCommand;
use GrapheneNodeClient\Commands\Login\LoginCommand;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Debug\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Debug\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Debug\TestCommand;
use GrapheneNodeClient\Tools\Auth;
use GrapheneNodeClient\Tools\ChainOperations\OpVote;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('PATH', __DIR__ . '/..');
require __DIR__ . "/Autoloader.php"; // only in GrapheneNodeClient project
require __DIR__ . '/../vendor/autoload.php';

echo "\n Test.php START";


////$connector = new SteemitWSConnector();
//$connector = new GolosWSConnector();
//$command = new GetDiscussionsByCreatedCommand($connector);
//$commandQueryData = new CommandQueryData();
//$data = [
//    'limit' => 100 //
//];
//$commandQueryData->setParams([$data]);
//for ($i = 1; $i < 100; $i++) {
//    echo "\n ---- call {$i}";
//    $data = [
//        'limit' => rand(80,100) //
//    ];
//    $commandQueryData->setParams([$data]);
//    $command->execute(
//        $commandQueryData
//    );
//}
//die;



$answer = OpVote::doSynchronous(
    Transaction::CHAIN_STEEM,
    'guest123',
    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg',
    'firepower',
    'steemit-veni-vidi-vici-steemfest-2016-together-we-made-it-happen-thank-you-steemians',
    10000
);

echo print_r($answer, true); die; //FIXME delete it
