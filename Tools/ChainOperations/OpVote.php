<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionCommand;
use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Tools\Auth;
use GrapheneNodeClient\Tools\Transaction;
use TrafficCophp\ByteBuffer\Buffer;

class OpVote
{
    /**
     * @param string  $platform
     * @param string  $voter
     * @param string  $publicWif
     * @param string  $author
     * @param string  $permlink
     * @param integer $weight
     *
     * @return mixed
     * @throws \Exception
     */
    public static function do($platform, $voter, $publicWif, $author, $permlink, $weight)
    {
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($platform);
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

        if (ConnectorInterface::PLATFORM_GOLOS === $platform) {
            $connector = new GolosWSConnector();
        } elseif (ConnectorInterface::PLATFORM_STEEMIT === $platform) {
            $connector = new SteemitWSConnector();
        }
        $command = new BroadcastTransactionCommand($connector);////        echo '<pre>' . var_dump($commandQueryData->getParams(), $properties2) . '<pre>'; die; //FIXME delete it
        Transaction::sign(Transaction::CHAIN_GOLOS, $tx, ['posting' => $publicWif]);
//        echo '<pre>' . var_dump($tx->getParams()) . '<pre>'; //FIXME delete it
        $answer = $command->execute(
            $tx
        );

        return $answer;
    }

    /**
     * @param string  $platform
     * @param string  $voter
     * @param string  $publicWif
     * @param string  $author
     * @param string  $permlink
     * @param integer $weight
     *
     * @return mixed
     * @throws \Exception
     */
    public static function doSynchronous($platform, $voter, $publicWif, $author, $permlink, $weight)
    {
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($platform);
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

        if (ConnectorInterface::PLATFORM_GOLOS === $platform) {
            $connector = new GolosWSConnector();
        } elseif (ConnectorInterface::PLATFORM_STEEMIT === $platform) {
            $connector = new SteemitWSConnector();
        }
        $command = new BroadcastTransactionSynchronousCommand($connector);////        echo '<pre>' . var_dump($commandQueryData->getParams(), $properties2) . '<pre>'; die; //FIXME delete it
        Transaction::sign(Transaction::CHAIN_GOLOS, $tx, ['posting' => $publicWif]);
//        echo '<pre>' . var_dump($tx->getParams()) . '<pre>'; //FIXME delete it
        $answer = $command->execute(
            $tx
        );

        return $answer;
    }


}