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

echo "\n WitnessUpdate.php START";


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
        'witness_update',
        [
            'owner'             => 'guest123',
            'url'               => 'https://golos.io/@guest123',
            'block_signing_key' => 'GLS7eExwRw2Waqrq7DcC1553revU7MWvjHMqK8sbWGScguest123',
            'props'             =>
                [
                    'account_creation_fee' => '0.001 GOLOS',
                    'maximum_block_size'   => 131072,
                    'sbd_interest_rate'    => 1000
                ],
            'fee'               => '0.000 GOLOS'
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
