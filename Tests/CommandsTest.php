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
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use PHPUnit\Framework\TestCase;

require "../vendor/autoload.php";

class CommandsTest extends TestCase
{
    public function testGetBlock()
    {
        $block_id = 777777;
        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $block_id);

        $command = new Commands(new SteemitHttpConnector());
        $command = $command->get_block();

        $data1 = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $data1);
        $this->assertArrayHasKey('witness', $data1['result']);
        //var_dump($data1);
    }

    public function testGetAccountHistory()
    {
        try {
            $acc = 'semasping';
            $from = -1;
            $limit = 0;
            $command = new Commands(new SteemitHttpConnector());
            $command = $command->get_account_history();

            $commandQuery = new CommandQueryData();
            $commandQuery->setParamByKey('0', $acc);
            $commandQuery->setParamByKey('1', $from);
            $commandQuery->setParamByKey('2', $limit);

            $content = $command->execute($commandQuery);
            $this->assertArrayHasKey('result', $content);
            var_dump($content['result'][0][0]);

        } catch (\Exception $e) {
            var_dump($e);
        }
    }

    public function testGetAccountHistory2()
    {
        $acc = 'semasping';
        $from = -1;
        $limit = 0;
        $command = new Commands(new SteemitHttpConnector());
        $command = $command->get_account_history();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', $acc);
        $commandQuery->setParamByKey('1', $from);
        $commandQuery->setParamByKey('2', $limit);

        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        var_dump($content['result'][0][0]);

    }

    public function testGetAccounts(){
        $acc = 'semasping';
        $command = new Commands(new SteemitHttpConnector());
        $command = $command->get_accounts();

        $commandQuery = new CommandQueryData();
        $commandQuery->setParamByKey('0', [$acc]);
        $content = $command->execute($commandQuery);
        $this->assertArrayHasKey('result', $content);
        //var_dump($content['result'][0]);

    }
}
