<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionCommand;
use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Tools\Transaction;

class OpTransfer
{
    /**
     * @param string  $chainName
     * @param string  $from
     * @param string  $privateActiveWif
     * @param string  $to
     * @param string  $amountWithAsset
     * @param string $memo
     *
     * @return mixed
     * @throws \Exception
     */
    public static function do($chainName, $from, $privateActiveWif, $to, $amountWithAsset, $memo)
    {
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($chainName);
        $tx->setParamByKey(
            '0:operations:0',
            [
                'transfer',
                [
                    'from'   => $from,
                    'to'     => $to,
                    'amount' => $amountWithAsset,
                    'memo'   => $memo
                ]
            ]
        );

        if (Transaction::CHAIN_GOLOS === $chainName) {
            $connector = new GolosWSConnector();
        } elseif (Transaction::CHAIN_STEEM === $chainName) {
            $connector = new SteemitWSConnector();
        }
        $command = new BroadcastTransactionCommand($connector);
        Transaction::sign($chainName, $tx, ['active' => $privateActiveWif]);

        $answer = $command->execute(
            $tx
        );

        return $answer;
    }

    /**
     * @param string  $chainName
     * @param string  $from
     * @param string  $privateActiveWif
     * @param string  $to
     * @param string  $amountWithAsset
     * @param string $memo
     *
     * @return mixed
     * @throws \Exception
     */
    public static function doSynchronous($chainName, $from, $privateActiveWif, $to, $amountWithAsset, $memo)
    {
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($chainName);
        $tx->setParamByKey(
            '0:operations:0',
            [
                'transfer',
                [
                    'from'   => $from,
                    'to'     => $to,
                    'amount' => $amountWithAsset,
                    'memo'   => $memo
                ]
            ]
        );

        if (Transaction::CHAIN_GOLOS === $chainName) {
            $connector = new GolosWSConnector();
        } elseif (Transaction::CHAIN_STEEM === $chainName) {
            $connector = new SteemitWSConnector();
        }
        $command = new BroadcastTransactionSynchronousCommand($connector);
        Transaction::sign($chainName, $tx, ['active' => $privateActiveWif]);

        $answer = $command->execute(
            $tx
        );

        return $answer;
    }


}