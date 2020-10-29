<?php


namespace GrapheneNodeClient\examples\Broadcast;

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Single\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Connectors\Http\GolosHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\Http\SteemitHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\Http\VizHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\Http\WhalesharesHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\VizWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\WhalesharesWSConnector;
use GrapheneNodeClient\Tools\Transaction;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/../../vendor/autoload.php';

echo "\n Donate.php START";


$connector = new GolosWSConnector();
//$connector = new SteemitWSConnector();
//$connector = new VizWSConnector();
//$connector = new WhalesharesWSConnector();
//$connector = new GolosHttpJsonRpcConnector();
//$connector = new SteemitHttpJsonRpcConnector();
//$connector = new VizHttpJsonRpcConnector();
//$connector = new WhalesharesHttpJsonRpcConnector();


//transfer agregation to few users
$chainName = $connector->getPlatform();
/** @var CommandQueryData $tx */
$tx = Transaction::init($connector);
$tx->setParamByKey(
    '0:operations:0',
    [
        'donate',
        [
            'from'       => 't3ran13',
            'to'         => 'redhat',
            'amount'     => '10.000 GOLOS',
            'memo'       =>
                [
                    'app'     => 'golos-id',
                    'version' => 1,
                    'target'  => [
                        'author'   => 'redhat',
                        'permlink' => 'vozmeshenii-ubytkov-prichinennykh-nekachestvennoi-uslugoi'
                    ],
                    'comment'  => 'test php--graphene-node-client'
                ],
            'extensions' => []
        ]
    ]
);
Transaction::sign($chainName, $tx, ['active' => '5_active_private_key']);

$command = new BroadcastTransactionSynchronousCommand($connector);
$answer = $command->execute(
    $tx
);



echo PHP_EOL . '<pre>' . print_r($answer, true) . '<pre>';
die; //FIXME delete it
