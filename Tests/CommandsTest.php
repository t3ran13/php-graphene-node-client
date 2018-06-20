<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 28.03.2018
 * Time: 12:57
 */

use GrapheneNodeClient\Commands\Commands;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Connectors\Http\SteemitHttpConnector;
use GrapheneNodeClient\Connectors\Http\SteemitHttpJsonRpcConnector;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Tools\ChainOperations\OpVote;
use PHPUnit\Framework\TestCase;

require "../vendor/autoload.php";

class CommandsTest extends TestCase
{
    public function connectorsDataProvider(){
        return [
            [new SteemitHttpJsonRpcConnector(),'steem'],
            [new GolosWSConnector(),'golos']
        ];
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetBlock($connect, $api)
    {
        $block_id = 777777;
        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block_id);

        $command = new Commands($connect);
        $command = $command->get_block();

        $data1 = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $data1);
        $this->assertArrayHasKey('witness', $data1['result']);
        //var_dump($data1);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetAccountHistory($connect, $api)
    {
        $acc = 'semasping';
        $from = -1;
        $limit = 0;
        $command = new Commands($connect);
        $command = $command->get_account_history();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $commandQuery->setParamByKey('1', $from);
        $commandQuery->setParamByKey('2', $limit);

        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);

    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetAccounts($connect, $api)
    {
        $acc = 'semasping';
        $command = new Commands($connect);
        $command = $command->get_accounts();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', [$acc]);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);


    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetDynamicGlobalProperties($connect, $api)
    {
        $command = new Commands($connect);
        $command = $command->get_dynamic_global_properties();

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);

    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetAccountCount($connect, $api)
    {
        $command = new Commands($connect);
        $command = $command->get_account_count();

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetAccountVotes($connect, $api)
    {
        $acc = 'semasping';
        $command = new Commands($connect);
        $command = $command->get_account_votes();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetActiveWitnesses($connect, $api)
    {
        $command = new Commands($connect);
        $command = $command->get_active_witnesses();

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetContent($connect, $api)
    {
        if ($api == 'golos') {
            $author = 'semasping';
            $permlink = 'accusta-zapusk-servisa-dlya-steemit';
        }
        if ($api == 'steem') {
            $author = 'semasping';
            $permlink = 'new-structure-of-commands-php-graphene-node-client';
        }
        $command = new Commands($connect);
        $command = $command->get_content();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $author);
        $commandQuery->setParamByKey('1', $permlink);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetBlockHeader($connect, $api)
    {
        $block_id = 777777;
        $command = new Commands($connect);
        $command = $command->get_block_header();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block_id);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetContentReplies($connect, $api)
    {
        if ($api == 'golos') {
            $author = 'semasping';
            $permlink = 'accusta-zapusk-servisa-dlya-steemit';
        }
        if ($api == 'steem') {
            $author = 'semasping';
            $permlink = 'new-structure-of-commands-php-graphene-node-client';
        }
        $command = new Commands($connect);
        $command = $command->get_content_replies();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $author);
        $commandQuery->setParamByKey('1', $permlink);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetCurrentMedianHistoryPrice($connect, $api)
    {
        $command = new Commands($connect);
        $command = $command->get_current_median_history_price();

        $commandQuery = new CommandQueryData();
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetDiscussionsByAuthorBeforeDate($connect, $api)
    {
        if ($api == 'golos') {
            $author = 'semasping';
            $permlink = 'accusta-zapusk-servisa-dlya-steemit';
            $date = '2018-01-01T00:00:00';
            $limit = 10;
        }
        if ($api == 'steem') {
            $author = 'semasping';
            $permlink = 'new-structure-of-commands-php-graphene-node-client';
            $date = '2018-01-01T00:00:00';
            $limit = 10;
        }
        $command = new Commands($connect);
        $command = $command->get_discussions_by_author_before_date();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $author);
        $commandQuery->setParamByKey('1', $permlink);
        $commandQuery->setParamByKey('2', $date);
        $commandQuery->setParamByKey('3', $limit);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetDiscussionsByBlog($connect, $api)
    {

        $tag = 'php';
        $limit = 10;
        $command = new Commands($connect);
        $command = $command->get_discussions_by_blog();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0:tag', $tag);
        $commandQuery->setParamByKey('0:limit', $limit);
        $commandQuery->setParamByKey('0:start_author', null);
        if ($api == 'golos')
            $commandQuery->setParamByKey('0:select_authors', ['semasping']);
        $commandQuery->setParamByKey('0:start_permlink', null);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetDiscussionsByCreated($connect, $api)
    {
        $tag = 'php';
        $limit = 10;
        $command = new Commands($connect);
        $command = $command->get_discussions_by_created();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0:tag', $tag);
        $commandQuery->setParamByKey('0:limit', $limit);
        $commandQuery->setParamByKey('0:start_author', null);
        $commandQuery->setParamByKey('0:start_permlink', null);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetDiscussionsByFeed($connect, $api)
    {
        $tag = 'php';
        $limit = 10;
        $command = new Commands($connect);
        $command = $command->get_discussions_by_feed();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0:tag', $tag);
        $commandQuery->setParamByKey('0:limit', $limit);
        $commandQuery->setParamByKey('0:start_author', null);
        $commandQuery->setParamByKey('0:start_permlink', null);
        if ($api == 'golos')
            $commandQuery->setParamByKey('0:select_authors', ['semasping']);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetDiscussionsByTrending($connect, $api)
    {
        $tag = 'php';
        $limit = 10;
        $command = new Commands($connect);
        $command = $command->get_discussions_by_trending();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0:tag', $tag);
        $commandQuery->setParamByKey('0:limit', $limit);
        $commandQuery->setParamByKey('0:start_author', null);
        $commandQuery->setParamByKey('0:start_permlink', null);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetOpsInBlock($connect, $api)
    {
        $block_id = 777777;
        $command = new Commands($connect);
        $command = $command->get_ops_in_block();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block_id);
        $commandQuery->setParamByKey('1', true);

        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetTrendingCategories($connect, $api)
    {
        $command = new Commands($connect);
        $command = $command->get_trending_tags();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', '');
        $commandQuery->setParamByKey('1', 10);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetWitnessesByVote($connect, $api)
    {
        $acc = 'arcange';
        $command = new Commands($connect);
        $command = $command->get_witnesses_by_vote();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $commandQuery->setParamByKey('1', 10);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

    /**
     * @dataProvider connectorsDataProvider
     */
    public function testGetFollowers($connect, $api)
    {
        $author = 'semasping';

        $command = new Commands($connect);
        $command = $command->get_followers();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $author);
        $commandQuery->setParamByKey('1', null);
        $commandQuery->setParamByKey('2', 'blog');
        $commandQuery->setParamByKey('3', 10);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);
    }

}
