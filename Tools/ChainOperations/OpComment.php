<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionCommand;
use GrapheneNodeClient\Commands\Broadcast\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Tools\Transaction;

class OpComment
{
    /**
     * @param string $chainName
     * @param string $privatePostingWif
     * @param string $author
     * @param string $permlink
     * @param string $title
     * @param string $body
     * @param string $jsonMetadata
     * @param string $parentPermlink
     * @param string $parentAuthor
     *
     * @return mixed
     * @throws \Exception
     */
    public static function do($chainName, $privatePostingWif, $author, $permlink, $title, $body, $jsonMetadata, $parentPermlink = '', $parentAuthor = '')
    {
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($chainName);
        $tx->setParamByKey(
            '0:operations:0',
            [
                'comment',
                [
                    'parent_author'   => $parentAuthor,
                    'parent_permlink' => $parentPermlink,
                    'author'          => $author,
                    'permlink'          => $permlink,
                    'title'           => $title,
                    'body'            => $body,
                    'json_metadata'   => $jsonMetadata
                ]
            ]
        );

        if (Transaction::CHAIN_GOLOS === $chainName) {
            $connector = new GolosWSConnector();
        } elseif (Transaction::CHAIN_STEEM === $chainName) {
            $connector = new SteemitWSConnector();
        }
        $command = new BroadcastTransactionCommand($connector);
        Transaction::sign($chainName, $tx, ['posting' => $privatePostingWif]);

        $answer = $command->execute(
            $tx
        );

        return $answer;
    }

    /**
     * @param string $chainName
     * @param string $privatePostingWif
     * @param string $author
     * @param string $permlink
     * @param string $title
     * @param string $body
     * @param string $jsonMetadata
     * @param string $parentPermlink
     * @param string $parentAuthor
     *
     * @return mixed
     * @throws \Exception
     */
    public static function doSynchronous($chainName, $privatePostingWif, $author, $permlink, $title, $body, $jsonMetadata, $parentPermlink = '', $parentAuthor = '')
    {
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($chainName);
        $tx->setParamByKey(
            '0:operations:0',
            [
                'comment',
                [
                    'parent_author'   => $parentAuthor,
                    'parent_permlink' => $parentPermlink,
                    'author'          => $author,
                    'permlink'          => $permlink,
                    'title'           => $title,
                    'body'            => $body,
                    'json_metadata'   => $jsonMetadata
                ]
            ]
        );

        if (Transaction::CHAIN_GOLOS === $chainName) {
            $connector = new GolosWSConnector();
        } elseif (Transaction::CHAIN_STEEM === $chainName) {
            $connector = new SteemitWSConnector();
        }
        $command = new BroadcastTransactionSynchronousCommand($connector);
        Transaction::sign($chainName, $tx, ['posting' => $privatePostingWif]);

        $answer = $command->execute(
            $tx
        );

        return $answer;
    }


}