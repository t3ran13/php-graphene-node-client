# php-graphene-node-client
PHP client for connection to STEEM/GOLOS node


## Install Via Composer
#### For readonly, without broadcast
```
composer require t3ran13/php-graphene-node-client
```
#### with broadcast (sending transactions to blockchain)
\(details and dockerfile [here](https://golos.io/ru--otkrytyij-kod/@php-node-client/podklyuchenie-secp256k1-php-k-php-dockerfile)\)

install components
- automake
- libtool
- libgmp-dev

install extensions
- secp256k1 \(how to install [secp256k1-php](https://github.com/Bit-Wasp/secp256k1-php)\)
- gmp



## Basic Usage
```php
<?php

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\DataBase\GetDiscussionsByCreatedCommand;
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

$command = new GetDiscussionsByCreatedCommand(new GolosWSConnector());
$golosPosts = $command->execute(
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
  
$command = new GetDiscussionsByCreatedCommand(new SteemitWSConnector());
$steemitPosts = $command->execute(
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

namespace: 
- GrapheneNodeClient\Commands\Broadcast;
- GrapheneNodeClient\Commands\DataBase;
- GrapheneNodeClient\Commands\Follow;
- GrapheneNodeClient\Commands\Login;

### database_api
- GetDynamicGlobalPropertiesCommand
- GetBlockCommand
- GetBlockHeaderCommand
- GetWitnessesByVoteCommand
- GetActiveWitnessesCommand
- GetAccountCommand
- GetAccountCountCommand
- GetAccountHistoryCommand
- GetAccountVotesCommand
- GetContentCommand
- GetDiscussionsByAuthorBeforeDateCommand
- GetDiscussionsByBlogCommand
- GetDiscussionsByCreatedCommand
- GetDiscussionsByFeedCommand
- GetDiscussionsByTrendingCommand
- GetTrendingCategoriesCommand
  
### login_api
- GetApiByNameCommand
- GetVersionCommand
- LoginCommand
   
  
### follow_api
- GetFollowersCommand
   
  
### broadcast_api
- BroadcastTransactionCommand
- BroadcastTransactionSynchronousCommand

### broadcast_api operations templates
- vote
- transfer
- comment 

```php
<?php

use GrapheneNodeClient\Tools\ChainOperations\OpVote;
use GrapheneNodeClient\Tools\Transaction;

$answer = OpVote::doSynchronous(
    Transaction::CHAIN_STEEM, //Transaction::CHAIN_GOLOS
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

namespace: GrapheneNodeClient\Connectors\WebSocket;

- GolosWSConnector (wss://ws.golos.io)
- SteemitWSConnector (wss://ws.steemit.com)

switch between connectors 
```php
<?php

use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\DataBase\GetContentCommand;
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

use GrapheneNodeClient\Commands\DataBase\CommandAbstract;
use GrapheneNodeClient\Connectors\ConnectorInterface;

class GolosWSConnector extends WSConnectorAbstract
{
    /**
     * @var string
     */
    protected $platform = self::PLATFORM_GOLOS;

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
```php
<?php

namespace My\App\Commands;

use GrapheneNodeClient\Commands\DataBase\CommandAbstract;
use GrapheneNodeClient\Connectors\ConnectorInterface;

class MyCommand extends CommandAbstract 
{
    protected $method            = 'method_name';
    //protected $apiName         = 'login_api'; in CommandAbstract have to be set correct $apiName
    
    //If different for platforms
    protected $queryDataMap = [
        ConnectorInterface::PLATFORM_GOLOS   => [
            //on the left is array keys and on the right is validators
            //validators for ani list element have to be have '*'  
            '*:limit'            => ['integer'], //the discussions return amount top limit
            '*:select_tags:*'    => ['nullOrString'], //list of tags to include, posts without these tags are filtered
            '*:select_authors:*' => ['nullOrString'], //list of authors to select
            '*:truncate_body'    => ['nullOrInteger'], //the amount of bytes of the post body to return, 0 for all
            '*:start_author'     => ['nullOrString'], //the author of discussion to start searching from
            '*:start_permlink'   => ['nullOrString'], //the permlink of discussion to start searching from
            '*:parent_author'    => ['nullOrString'], //the author of parent discussion
            '*:parent_permlink'  => ['nullOrString'] //the permlink of parent discussion
        ],
        ConnectorInterface::PLATFORM_STEEMIT => [
            //for list params
            '*:tag'            => ['nullOrString'], //'author',
            '*:limit'          => ['integer'], //'limit'
            '*:start_author'   => ['nullOrString'], //'start_author' for pagination,
            '*:start_permlink' => ['nullOrString'] //'start_permlink' for pagination,
        ]
    ];
    
    
    //If the same for platforms
    //protected $queryDataMap = [
    // route example: 'key:123:array' => $_SESSION['key'][123]['array']
    //    'some_array_key:some_other_key' => ['integer'],   // available validators are 'required', 'array', 'string',
                                                            // 'integer', 'nullOrArray', 'nullOrString', 'nullOrInteger'.
    //];
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


## Transaction for blockchain (broadcast)


```php
<?php

use GrapheneNodeClient\Tools\Transaction;

/** @var CommandQueryData $tx */
$tx = Transaction::init($chainName);
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

if (Transaction::CHAIN_GOLOS === $chainName) {
    $connector = new GolosWSConnector();
} elseif (Transaction::CHAIN_STEEM === $chainName) {
    $connector = new SteemitWSConnector();
}
$command = new BroadcastTransactionSynchronousCommand($connector);
Transaction::sign($chainName, $tx, ['posting' => $publicWif]);

$answer = $command->execute(
    $tx
);

```
** WARNING**
Transactions are signing with spec256k1-php with function secp256k1_ecdsa_sign_recoverable($context, $signatureRec, $msg32, $privateKey) and if it is not canonical from first time, you have to make transaction for other block. For searching canonical sign function have to implement two more parameters, but spec256k1-php library does not have it.