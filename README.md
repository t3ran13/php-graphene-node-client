# php-graphene-node-client
PHP client for connection to STEEM/GOLOS node


## Install Via Composer
#### For readonly, without broadcast
```
composer require t3ran13/php-graphene-node-client
```
#### with broadcast (sending transactions to blockchain)
\(details are [here](https://golos.io/ru--otkrytyij-kod/@php-node-client/podklyuchenie-secp256k1-php-k-php-dockerfile)\) and actual dockerfile and requests examples see in branch ["debug"]()https://github.com/t3ran13/php-graphene-node-client/tree/debug)

install components
- automake
- libtool
- libgmp-dev

install extensions
- Bit-Wasp/secp256k1-php v0.2.1 \(how to install [secp256k1-php](https://github.com/Bit-Wasp/secp256k1-php)\)
- gmp



## Basic Usage
```php
<?php

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Commands;
use GrapheneNodeClient\Commands\Single\GetDiscussionsByCreatedCommand;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;


//Set params for query
$commandQuery = new CommandQueryData();
$data = [
    [
        'limit'       => $limit,
        'select_tags' => ['golos'], // for GOLOS
        'tag'         => 'steemit', // for STEEMIT     
    ]
];
$commandQuery->setParams($data);

//OR 
$commandQuery = new CommandQueryData();
$commandQuery->setParamByKey('0:limit', $limit);
$commandQuery->setParamByKey('0:select_tags', [$tag]);
$commandQuery->setParamByKey('0:tag', $tag);


//and use single command
$command = new GetDiscussionsByCreatedCommand(new GolosWSConnector());
$golosPosts = $command->execute(
    $commandQuery
);

//or commands aggregator class
$commands = new Commands(new GolosWSConnector());
$golosPosts = $commands->get_discussions_by_created()
    ->execute(
       $commandQuery
);


// will return
// [
//      "id" => 1,
//      "result" => [
//            [
//                "id": 466628,
//                "author": "piranya",
//                "permlink": "devyatyi-krug",
//                ...
//            ],
//            ...
//      ]
// ]
  
  
//single command  
$command = new GetDiscussionsByCreatedCommand(new SteemitWSConnector());
$steemitPosts = $command->execute(
    $commandQuery,
    'result',
    SteemitWSConnector::ANSWER_FORMAT_ARRAY // or SteemitWSConnector::ANSWER_FORMAT_OBJECT
);

//or commands aggregator class
$commands = new Commands(new GolosWSConnector());
$golosPosts = $commands->get_discussions_by_created()
    ->execute(
        $commandQuery,
        'result',
        SteemitWSConnector::ANSWER_FORMAT_ARRAY // or SteemitWSConnector::ANSWER_FORMAT_OBJECT
);


// will return
// [
//      [
//          "id": 466628,
//          "author": "piranya",
//          "permlink": "devyatyi-krug",
//          ...
//      ],
//      ...
// ]


```
  
   

## Implemented Commands List

### Single Commands
- BroadcastTransactionCommand
- BroadcastTransactionSynchronousCommand
- GetAccountCountCommand
- GetAccountHistoryCommand
- GetAccountsCommand
- GetAccountVotesCommand
- GetActiveWitnessesCommand
- GetApiByNameCommand //ONLY STEEM
- GetBlockCommand
- GetBlockHeaderCommand
- GetConfigCommand
- GetContentCommand
- GetContentRepliesCommand
- GetCurrentMedianHistoryPriceCommand
- GetDiscussionsByAuthorBeforeDateCommand
- GetDiscussionsByBlogCommand
- GetDiscussionsByCreatedCommand
- GetDiscussionsByFeedCommand
- GetDiscussionsByTrendingCommand
- GetDynamicGlobalPropertiesCommand
- GetFollowersCommand
- GetOpsInBlock
- GetTrendingCategoriesCommand
- GetVersionCommand
- GetWitnessesByVoteCommand
- LoginCommand //ONLY STEEM

All single commands can be called through Commands Class as methods (example: (new Commands)->get_block()->execute(...) )


### broadcast operations templates

namespace GrapheneNodeClient\Tools\ChainOperations

- vote
- transfer
- comment 

```php
<?php

use GrapheneNodeClient\Tools\ChainOperations\OpVote;
use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;

$connector = new SteemitHttpConnector();
//$connector = new GolosWSConnector();

$answer = OpVote::doSynchronous(
    $connector,
    'guest123',
    '5JRaypasxMx1L97ZUX7YuC5Psb5EAbF821kkAGtBj7xCJFQcbLg',
    'firepower',
    'steemit-veni-vidi-vici-steemfest-2016-together-we-made-it-happen-thank-you-steemians',
    10000
);

// example of answer
//Array
//(
//    [id] => 5
//    [result] => Array
//        (
//            [id] => a2c52988ea870e446480782ff046994de2666e0d
//            [block_num] => 17852337
//            [trx_num] => 1
//            [expired] =>
//        )
//
//)

```

## Implemented Connectors List

namespace: GrapheneNodeClient\Connectors\WebSocket OR GrapheneNodeClient\Connectors\Http;

- GolosWSConnector (wss://ws.golos.io)
- SteemitWSConnector (wss://steemd.minnowsupportproject.org)
- SteemitHttpConnector (https://steemd.privex.io)

List of available STEEM nodes are [here](https://www.steem.center/index.php?title=Public_Websocket_Servers)


#### Switching between connectors 
```php
<?php

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\Single\GetContentCommand;
use GrapheneNodeClient\Connectors\InitConnector;

$command = new GetContentCommand(InitConnector::getConnector(InitConnector::PLATFORM_STEEMIT));

$commandQuery = new CommandQueryData();
$commandQuery->setParamByKey('0', 'author');
$commandQuery->setParamByKey('1', 'permlink');

//OR
$commandQuery = new CommandQueryData();
$commandQuery->setParams(
    [
        0 => "author",
        1 => "permlink"
    ]
);

$content = $command->execute(
    $commandQuery
);
// will return
// [
//      "id" => 1,
//      "result" => [
//            ...
//      ]
// ]


```

   

## Creating Own Connector
```php
<?php

namespace My\App\Connectors;

use GrapheneNodeClient\Connectors\ConnectorInterface;

class MyConnector implements ConnectorInterface 
{
    public function setConnectionTimeoutSeconds($timeoutSeconds) {
     // TODO: Implement setConnectionTimeoutSeconds() method.
    }
    
    public function setMaxNumberOfTriesToReconnect($triesN) {
     // TODO: Implement setMaxNumberOfTriesToReconnect() method.
    }
    
    /**
    * platform name for witch connector is. steemit or golos.
    */
    public function getPlatform() {
     // TODO: Implement getPlatform() method.
    }
    
    /**
    * @param string $apiName calling api name - follow_api, database_api and ect.
    * @param array  $data    options and data for request
    * @param string $answerFormat
    *
    * @return array|object return answer data
    */
    public function doRequest($apiName, array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY) {
     // TODO: Implement doRequest() method.
    }

}


```
Or use GrapheneNodeClient\Connectors\WebSocket\WSConnectorAbstract for extending

```php
<?php

namespace My\App\Commands;

use GrapheneNodeClient\Commands\Single\CommandAbstract;
use GrapheneNodeClient\Connectors\ConnectorInterface;

class GolosWSConnector extends WSConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_GOLOS;

    /**
     * waiting answer from Node during $wsTimeoutSeconds seconds
     *
     * @var int
     */
    protected $wsTimeoutSeconds = 5;

    /**
     * max number of tries to get answer from the node
     *
     * @var int
     */
    protected $maxNumberOfTriesToCallApi = 3;

    /**
     * wss or ws servers, can be list. First node is default, other are reserve.
     * After $maxNumberOfTriesToCallApi tries connects to default it is connected to reserve node.
     *
     * @var string|array
     */
    protected $nodeURL = ['wss://ws.golos.io', 'wss://api.golos.cf'];
}


```  

   
  
   

## Creating Own Command

You have to update $steemAPI/$golosAPI properties in class GrapheneNodeClient\Commands\Commands.php as shown below

```php
<?php


namespace GrapheneNodeClient\Commands;


use GrapheneNodeClient\Connectors\ConnectorInterface;

/**
 * @method Commands broadcast_transaction()
 * //...
 * @method Commands your_method()
 */
class Commands implements CommandInterface
{
    //...
    //protected $projectApi = [ 'method_name' => [ 'apiName' => 'api_name', 'fields'=>['массив с полями из команды']]];
    protected $steemAPI = [
        //...
        'broadcast_transaction'                 => [
            'apiName' => 'network_broadcast_api',
            'fields'  => [
                '0:ref_block_num'    => ['integer'],
                '0:ref_block_prefix' => ['integer'],
                '0:expiration'       => ['string'],
                '0:operations:*:0'   => ['string'],
                '0:operations:*:1'   => ['array'],
                '0:extensions'       => ['array'],
                '0:signatures'       => ['array']
            ]
        ],
        //...
        'broadcast_transaction'                 => [
            'apiName' => 'your_method',
            'fields'  => [
                //your fields
            ]
        ]
    ];
}


```  

# Tools
## Transliterator


```php
<?php

use GrapheneNodeClient\Tools\Transliterator;


//Encode tags
$tag = Transliterator::encode('пол', Transliterator::LANG_RU); // return 'pol';


//Decode tags
$tag = Transliterator::encode('ru--pol', Transliterator::LANG_RU); // return 'пол';

```


## Reputation viewer


```php
<?php

use GrapheneNodeClient\Tools\Reputation;

$rep = Reputation::calculate($account['reputation']);

```

## Bandwidth

Sometimes you can't send transaction to blockchain because your account has not enough bandwidth. Now you can check this before sending transaction to blockchain as shown below


```php
<?php

use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Tools\Bandwidth;
use GrapheneNodeClient\Tools\Transaction;


$connector = new SteemitHttpConnector();
/** @var CommandQueryData $tx */
$tx = Transaction::init($connector, 'PT4M');
$tx->setParamByKey(
    '0:operations:0',
    [
        'vote',
        [
            'voter'    => $voter,
            'author'   => $author,
            'permlink' => $permlink,
            'weight'   => $weight
        ]
    ]
);
$command = new BroadcastTransactionSynchronousCommand($connector);
Transaction::sign($chainName, $tx, ['posting' => $publicWif]);

$bandwidth = Bandwidth::getBandwidthByAccountName($this->rewardPoolName, 'market', $connector);

//Array
//(
//    [used] => 3120016
//    [available] => 148362781
//)

$trxString = mb_strlen(json_encode($tx->getParams()), '8bit');

if ($trxString * 10 + $bandwidth['used'] < $bandwidth['available']) {
	$answer = $command->execute(
		$tx
	);
}



```


## Transaction for blockchain (broadcast)


```php
<?php

use GrapheneNodeClient\Tools\Transaction;
use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;

$connector = new SteemitHttpConnector();
//$connector = new GolosWSConnector();

/** @var CommandQueryData $tx */
$tx = Transaction::init($connector, 'PT4M');
$tx->setParamByKey(
    '0:operations:0',
    [
        'vote',
        [
            'voter'    => $voter,
            'author'   => $author,
            'permlink' => $permlink,
            'weight'   => $weight
        ]
    ]
);

$command = new BroadcastTransactionSynchronousCommand($connector);
Transaction::sign($chainName, $tx, ['posting' => $publicWif]);

$answer = $command->execute(
    $tx
);

```


** WARNING**

Transactions are signing with spec256k1-php with function secp256k1_ecdsa_sign_recoverable($context, $signatureRec, $msg32, $privateKey) and if it is not canonical from first time, you have to make transaction for other block. For searching canonical sign function have to implement two more parameters, but spec256k1-php library does not have it.
It is was solved with php-hack in Transaction::sign()
```php
...
//becouse spec256k1-php canonical sign trouble will use php hack.
//If sign is not canonical, we have to chang msg (we will add 1 sec to tx expiration time) and try to sign again
$nTries = 0;
while (true) {
    $nTries++;
    $msg = self::getTxMsg($chainName, $trxData);
    echo '<pre>' . print_r($trxData->getParams(), true) . '<pre>'; //FIXME delete it

    try {
        foreach ($privateWIFs as $keyName => $privateWif) {
            $index = count($trxData->getParams()[0]['signatures']);

            /** @var CommandQueryData $trxData */
            $trxData->setParamByKey('0:signatures:' . $index, self::signOperation($msg, $privateWif));
        }
        break;
    } catch (TransactionSignException $e) {
        if ($nTries > 200) {
            //stop tries to find canonical sign
            throw $e;
            break;
        } else {
            /** @var CommandQueryData $trxData */
            $params = $trxData->getParams();
            foreach ($params as $key => $tx) {
                $tx['expiration'] = (new \DateTime($tx['expiration']))
                    ->add(new \DateInterval('PT0M1S'))
                    ->format('Y-m-d\TH:i:s\.000');
                $params[$key] = $tx;
            }
            $trxData->setParams($params);
        }
    }
...
```

## Tests
You need to install PhpUnit in your system (https://phpunit.de/manual/3.7/en/installation.html)
```
cd Tests
phpunit CommandsTest.php 
phpunit CommandsTest.php --filter=testGetBlock // test only one command
```