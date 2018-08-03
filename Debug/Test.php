<?php


namespace GrapheneNodeClient\Debug;

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Commands;
use GrapheneNodeClient\Commands\Single\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Commands\Single\GetAccountsCommand;
use GrapheneNodeClient\Commands\Single\GetContentCommand;
use GrapheneNodeClient\Commands\Single\GetDiscussionsByCreatedCommand;
use GrapheneNodeClient\Commands\Single\GetDynamicGlobalPropertiesCommand;
use GrapheneNodeClient\Commands\Single\GetOpsInBlock;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Connectors\Http\GolosHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\Http\SteemitHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Debug\TestCommand;
use GrapheneNodeClient\Tools\Auth;
use GrapheneNodeClient\Tools\Bandwidth;
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


//$connector = new GolosWSConnector();
$connector = new SteemitWSConnector();
//$connector = new SteemitHttpJsonRpcConnector(1000);
//$connector = new GolosHttpConnector();
//$command = new GetDynamicGlobalPropertiesCommand($connector);
//$commandQueryData = new CommandQueryData();
//$answer = $command->execute(
//    $commandQueryData,
//    'result'
//);




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
//$title = 'Test posting from php client again' . time() . 'Test posting from php client again' . time();
//$title2 = $title;
//$title2 = 'Тест с русским языком';
//$answer = OpComment::doSynchronous(
//    $connector,
////    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg', //steem
//    '5JoCVxmzMVgRb35ZXXoAP4dtTaRnPCEa6o5wwMjaroBSh8oPHPx', //golos
//    'guest123',
//    strtolower(str_replace(' ', '-', $title)),
//    $title2,
////    $title,
//    '## h2 '
//    . '<br> details in blog @php-node-client'
//    . '<br> еще приписка на русском'
//    . '<br>лоролрд',
//    json_encode(['tags' => ['test', 'php']]), //'{"tags":["test","php"]}',
//    'test',
//    ''
//);

//transfer tokens
//$answer = OpTransfer::doSynchronous(
//    $connector,
//    '5_active_key',
//    'guest123',
//    'php-node-client',
//    '0.010 SBD',
//    'test php transfer of SBD'
//);



////transfer agregation to few users
$chainName = $connector->getPlatform();
/** @var CommandQueryData $tx */
$tx = Transaction::init($connector);
$tx->setParamByKey(
    '0:operations:0',
    [
        'transfer',
        [
            'from'   => 'guest123',
            'to'     => 't3ran13',
            'amount' => '0.002 SBD',
            'memo'   => '6  Reward authors from The Alternative STEEM TOPs https://steemit.com/top/@t3ran13/the-alternative-steem-tops-06-05-2018-gmt-top-of-the-pop'
        ]
    ]
);
$tx->setParamByKey(
    '0:operations:1',
    [
        'transfer',
        [
            'from'   => 'guest123',
            'to'     => 'xroni',
            'amount' => '0.003 SBD',
            'memo'   => '6 111111111111111111111111111111111111111111111111111111111111111111 Reward authors from The Alternative STEEM TOPs https://steemit.com/top/@t3ran13/the-alternative-steem-tops-06-05-2018-gmt-top-of-the-pop'
        ]
    ]
);
Transaction::sign($chainName, $tx, ['active' => '5_active_key']);

$command = new BroadcastTransactionSynchronousCommand($connector);
$answer = $command->execute(
    $tx
);


//repost in custom_json
//$chainName = $connector->getPlatform();
///** @var CommandQueryData $tx */
//$tx = Transaction::init($connector);
//$tx->setParamByKey(
//    '0:operations:0',
//    [
//        'custom_json',
//        [
//            'required_auths'         => [],
//            'required_posting_auths' => ['guest123'],
//            'id'                     => 'follow',
//            'json'                   => json_encode(
//                [
//                    'reblog',
//                    [
//                        'account'  => 'guest123',
//                        'author'   => 'lex',
//                        'permlink' => 'zakony-na-potoke-ili-mnogo-zakonov-no-malo-zakonnosti'
//                    ]
//                ]
//            )
//        ]
//    ]
//);
//Transaction::sign(
//    $chainName,
//    $tx,
//    [
//        'posting' => '5JoCVxmzMVgRb35ZXXoAP4dtTaRnPCEa6o5wwMjaroBSh8oPHPx', //golos
//    ]
//);
//
//$command = new BroadcastTransactionSynchronousCommand($connector);
//$answer = $command->execute(
//    $tx
//);


//upvote post or comment
//$answer = OpVote::doSynchronous(
//    $connector,
//    'guest123',
//    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg', //steem
////    '5JoCVxmzMVgRb35ZXXoAP4dtTaRnPCEa6o5wwMjaroBSh8oPHPx', //golos
//    'siddxa',
//    're-goloscore-novosti-golos-core-khardfork-17-prinyat-plany-na-khardfork-18-20180405t153333452z',
//    10000
//);

//$block_id = 777777;
//$commandQuery = new CommandQueryData();
//$commandQuery->setParamByKey('0', $block_id);
//
//$command = new Commands($connector );
//$command = $command->get_block();
////
//$answer = $command->execute($commandQuery);

//$block_id = 22482921;
//$commandQuery = new CommandQueryData();
//$commandQuery->setParamByKey('0', $block_id);//blockNum
//$commandQuery->setParamByKey('1', false);//onlyVirtual
//
//$command = new GetOpsInBlock($connector);
//$answer = $command->execute(
//    $commandQuery,
//    'result'
//);


//$startMTime = microtime(true);
//$command = new GetDiscussionsByCreatedCommand($connector);
//$commandQueryData = new CommandQueryData();
//$data = [
//    'limit' => 27 //
//];
//$commandQueryData->setParams([$data]);
//$answer = $command->execute(
//    $commandQueryData
//);
//$timeout = $requestTimeout = microtime(true) - $startMTime;
//echo PHP_EOL . " -- total: " . count($answer['result']) . ' ';
//echo PHP_EOL . " -- timeout: " . $timeout . ' ';
//
//$emptyCounter = 0;
//$startMTime = microtime(true);
//foreach ($answer['result'] as $key => $disc) {
//    //post was deleted
//    if (isset($disc['created']) && $disc['created'] === '1970-01-01T00:00:00') {
//        echo "\n discussion was deleted";
//        continue;
//    }
//    //wrong api answer
//    if (
//        empty($disc['author'])
//        || empty($disc['permlink'])
//    ) {
//        $emptyCounter++;
//    }
//
//    $commandQuery = new CommandQueryData();
//    $commandQuery->setParamByKey('0', $disc['author']);//blockNum
//    $commandQuery->setParamByKey('1', $disc['permlink']);//onlyVirtual
//
//    $command = new GetContentCommand($connector);
//    $answer = $command->execute(
//        $commandQuery
//    );
//    //if got wrong answer from api
//    if (!isset($answer['result'])) {
//        $emptyCounter++;
//    } elseif (empty($answer['result'])) {
//        $emptyCounter++;
//    }
//}
//echo PHP_EOL . " -- empty: " . $emptyCounter . ' ';
//$timeout = $requestTimeout = microtime(true) - $startMTime;
//echo PHP_EOL . " -- timeout: " . $timeout . ' ';

//$answer = Bandwidth::getBandwidthByAccountName('golos-top-newbie', 'market', $connector);




//$commandQuery = new CommandQueryData();
//$command = new GetDynamicGlobalPropertiesCommand($connector);
//$answer = $command->execute(
//    $commandQuery
//);


echo '<pre>' . print_r($answer, true) . '<pre>'; die; //FIXME delete it
