# php-graphene-node-client
PHP client for connection to Graphene node


## Install Via Composer
```
composer require t3ran13/php-graphene-node-client
```

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

namespace: GrapheneNodeClient\Commands\DataBase;

- GetContentCommand
- GetDiscussionsByAuthorBeforeDateCommand
- GetDiscussionsByBlogCommand
- GetDiscussionsByFeedCommand
- GetDiscussionsByCreatedCommand
- GetDiscussionsByTrendingCommand
- GetTrendingCategoriesCommand
  
   

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
    public function getPlatform() {
     // TODO: Implement getPlatform() method.
    }
    
    public function doRequest(array $data, $answerFormat = self::ANSWER_FORMAT_ARRAY) {
     // TODO: Implement doRequest() method.
    }
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
