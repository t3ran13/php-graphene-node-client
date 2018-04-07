<?php


namespace GrapheneNodeClient\Debug;

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Single\GetAccountsCommand;
use GrapheneNodeClient\Commands\Single\GetDiscussionsByCreatedCommand;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
use GrapheneNodeClient\Debug\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Debug\TestCommand;
use GrapheneNodeClient\Tools\Auth;
use GrapheneNodeClient\Tools\ChainOperations\OpComment;
use GrapheneNodeClient\Tools\ChainOperations\OpTransfer;
use GrapheneNodeClient\Tools\ChainOperations\OpVote;
use GrapheneNodeClient\Tools\Transaction;
use t3ran13\ByteBuffer\ByteBuffer;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('PATH', __DIR__ . '/..');
require __DIR__ . "/Autoloader.php"; // only in GrapheneNodeClient project
require __DIR__ . '/../vendor/autoload.php';

echo "\n Test.php START";


//$connector = new SteemitWSConnector();
//$connector = new SteemitHttpConnector();
$connector = new GolosWSConnector();
//$command = new GetDiscussionsByCreatedCommand($connector);
//$commandQueryData = new CommandQueryData();
//$data = [
//    'limit' => 100 //
//];
//$commandQueryData->setParams([$data]);
//for ($i = 1; $i < 100; $i++) {
//    echo "\n ---- call {$i}";
//    $data = [
//        'limit' => rand(2,4) //
//    ];
//
//    if (isset($startAuthor) && isset($startPermlink)) {
//        $data['start_author'] = $startAuthor;
//        $data['start_permlink'] = $startPermlink;
//    }
//
//    $commandQueryData->setParams([$data]);
//    $answer = $command->execute(
//        $commandQueryData
//    );
//    echo PHP_EOL . print_r(count($answer['result']), true);
//    $post = array_pop($answer['result']);
//    $startAuthor = $post['author'];
//    $startPermlink = $post['permlink'];
//}
//die;
//
//
//$command = new GetAccountsCommand($connector);
//$commandQueryData = new CommandQueryData();
//$commandQueryData->setParams(
//    [
//        0 => ['t3ran13','semasping']
//    ]
//);
//$answer = $command->execute(
//    $commandQueryData
//);
//echo PHP_EOL . print_r($answer['result'], true);
//die;


//make POST
$title = 'Test posting from php client again' . time() . 'Test posting from php client again' . time();
$title2 = $title;
$title2 = 'Тест с русским языком';
$answer = OpComment::doSynchronous(
    $connector,
//    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg', //steem
    '5JoCVxmzMVgRb35ZXXoAP4dtTaRnPCEa6o5wwMjaroBSh8oPHPx', //golos
    'guest123',
    strtolower(str_replace(' ', '-', $title)),
    $title2,
//    $title,
    '## h2 '
    . '<br> details in blog @php-node-client'
    . '<br> еще приписка на русском'
    . '<br>5CVxmzMVgRb35ZXXoAP4dtTaRnPCEa6o5wwMjaroBSh8oPHPx',
    json_encode(['tags' => ['test', 'php']]), //'{"tags":["test","php"]}',
    'test',
    ''
);

//transfer tokens
//$answer = OpTransfer::doSynchronous(
//    $connector,
//    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg',
//    'guest123',
//    'php-node-client',
//    '0.010 SBD',
//    'test php transfer of SBD'
//);

//upvote post or comment
//$answer = OpVote::doSynchronous(
//    $connector,
//    'guest123',
//    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg', //steem
////    '5JoCVxmzMVgRb35ZXXoAP4dtTaRnPCEa6o5wwMjaroBSh8oPHPx', //golos
//    'andyhoffman',
//    'use-this-version-andy-hoffman-cryptogoldcentral-com-special-crypto-audioblog-bitcoin-community-unite-against-bcash',
//    10000
//);
echo '<pre>' . print_r($answer, true) . '<pre>'; die; //FIXME delete it
