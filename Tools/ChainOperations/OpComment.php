<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use GrapheneNodeClient\Commands\Single\BroadcastTransactionCommand;
use GrapheneNodeClient\Commands\Single\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Tools\Transaction;

class OpComment
{
    /**
     * @param ConnectorInterface $connector
     * @param string             $privatePostingWif
     * @param string             $author
     * @param string             $permlink
     * @param string             $title
     * @param string             $body
     * @param string             $jsonMetadata
     * @param string             $parentPermlink
     * @param string             $parentAuthor
     *
     * @return mixed
     * @throws \Exception
     */
    public static function do(ConnectorInterface $connector, $privatePostingWif, $author, $permlink, $title, $body, $jsonMetadata, $parentPermlink = '', $parentAuthor = '')
    {
        $chainName = $connector->getPlatform();
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($connector);
        $tx->setParamByKey(
            '0:operations:0',
            [
                'comment',
                [
                    'parent_author'   => $parentAuthor,
                    'parent_permlink' => $parentPermlink,
                    'author'          => $author,
                    'permlink'        => $permlink,
                    'title'           => $title,
                    'body'            => $body,
                    'json_metadata'   => $jsonMetadata
                ]
            ]
        );

        $command = new BroadcastTransactionCommand($connector);
        Transaction::sign($chainName, $tx, ['posting' => $privatePostingWif]);

        $answer = $command->execute(
            $tx
        );

        return $answer;
    }

    /**
     * @param ConnectorInterface $connector
     * @param string             $privatePostingWif
     * @param string             $author
     * @param string             $permlink
     * @param string             $title
     * @param string             $body
     * @param string             $jsonMetadata
     * @param string             $parentPermlink
     * @param string             $parentAuthor
     *
     * @return mixed
     * @throws \Exception
     */
    public static function doSynchronous(ConnectorInterface $connector, $privatePostingWif, $author, $permlink, $title, $body, $jsonMetadata, $parentPermlink = '', $parentAuthor = '')
    {
        $chainName = $connector->getPlatform();
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($connector);
        $tx->setParamByKey(
            '0:operations:0',
            [
                'comment',
                [
                    'parent_author'   => $parentAuthor,
                    'parent_permlink' => $parentPermlink,
                    'author'          => $author,
                    'permlink'        => $permlink,
                    'title'           => $title,
                    'body'            => $body,
                    'json_metadata'   => $jsonMetadata
                ]
            ]
        );

        $command = new BroadcastTransactionSynchronousCommand($connector);
        Transaction::sign($chainName, $tx, ['posting' => $privatePostingWif]);

        $answer = $command->execute(
            $tx
        );

        return $answer;
    }


}