# php-graphene-node-client
PHP client for connection to Graphene node


## Install Via Composer
```
composer require t3ran13/php-graphene-node-client
```

## Basic Usage
```php
<?php

use GrapheneNodeClient\Commands\GetTrendingCategoriesCommand;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;

$command = new GetTrendingCategoriesCommand(new GolosWSConnector());

$trendingTags = $command->execute(
    [
        "", //'after'
        2 //'limit'
    ]
);
// will return
// [
//      "id" => 2,
//      "result" => [
//            [
//                "name" => "ru--zhiznx",
//                "total_payouts": "410233.942 GBG",
//                ...
//            ],
//            ...
//      ]
// ]
  
$trendingTags = $command->execute(
    [
        "", //'after'
        2 //'limit'
    ],
    'result',
    GolosWSConnector::ANSWER_FORMAT_ARRAY // or GolosWSConnector::ANSWER_FORMAT_OBJECT
);
// will return
// [
//      [
//          "name" => "ru--zhiznx",
//          "total_payouts": "410233.942 GBG",
//          ...
//      ],
//      ...
// ]


```
  
   

## Implemented Commands List

namespace: GrapheneNodeClient\Commands;

- GetContentCommand
- GetDiscussionsByAuthorBeforeDateCommand
- GetDiscussionsByBlogCommand
- GetDiscussionsByCreatedCommand
- GetTrendingCategoriesCommand
  
   

## Implemented Connectors List

namespace: GrapheneNodeClient\Connectors\WebSocket;

- GolosWSConnector
- SteemitWSConnector

switch between connectors 
```php
<?php

use GrapheneNodeClient\Commands\GetContentCommand;
use GrapheneNodeClient\Connectors\InitConnector;

$command = new GetContentCommand(InitConnector::getConnector(InitConnector::PLATFORM_STEEMIT));

$content = $command->execute(
    [
        0 => "author",
        1 => "permlink"
    ]
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
    public function doRequest(array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY) {
     // TODO: Implement doRequest() method.
    }
}


```
  
   

## Creating Own Command
```php
<?php

namespace My\App\Commands;

use GrapheneNodeClient\Commands\CommandAbstract;

class MyCommand extends CommandAbstract 
{
    protected $method            = 'method_name';
    
    protected $requiredParams = [
        // for list params
        0 => [
            'param_key1', //this key will be required
            'param_key2', //this key will be required
        ]
        //or 
        //'param_key1', //this key will be required
        //'param_key2', //this key will be required
    ];
}


```
