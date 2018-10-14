<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use GrapheneNodeClient\Commands\Single\BroadcastTransactionCommand;
use GrapheneNodeClient\Commands\Single\BroadcastTransactionSynchronousCommand;
use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Tools\Transaction;

//ONLY VIZ
class OpContent
{
    /**
     * @param ConnectorInterface $connector
     * @param string             $privatePostingWif
     * @param string             $author
     * @param string             $permlink
     * @param string             $title
     * @param string             $body
     * @param string             $jsonMetadata
     * @param integer            $curationPercent 10000 as 100%
     * @param array[]            $beneficiaries as array [['account' => 'denis-skripnik', 'weight' => 10000]], weight is max 10000 as 100%
     * @param string             $parentPermlink
     * @param string             $parentAuthor
     *
     * @return mixed
     * @throws \Exception
     */
    public static function do(ConnectorInterface $connector, $privatePostingWif, $author, $permlink, $title, $body, $jsonMetadata, $curationPercent = 0, $beneficiaries = [], $parentPermlink, $parentAuthor = '')
    {
        $chainName = $connector->getPlatform();

        /** @var CommandQueryData $tx */
        $tx = Transaction::init($connector);
        $tx->setParamByKey(
            '0:operations:0',
            [
                ChainOperations::OPERATION_CONTENT,
                [
                    'parent_author'   => $parentAuthor,
                    'parent_permlink' => $parentPermlink,
                    'author'          => $author,
                    'permlink'        => $permlink,
                    'title'           => $title,
                    'body'            => $body,
                    'curation_percent'=> $curationPercent,
                    'json_metadata'   => $jsonMetadata,
                    'extensions'      => empty($beneficiaries) ? [] : [[0, ["beneficiaries" => $beneficiaries]]]
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
     * @param integer            $curationPercent 10000 as 100%
     * @param array[]            $beneficiaries as array [['account' => 'denis-skripnik', 'weight' => 10000]], weight is max 10000 as 100%
     * @param string             $parentPermlink
     * @param string             $parentAuthor
     *
     * @return mixed
     * @throws \Exception
     */
    public static function doSynchronous(ConnectorInterface $connector, $privatePostingWif, $author, $permlink, $title, $body, $jsonMetadata, $curationPercent = 0, $beneficiaries = [], $parentPermlink, $parentAuthor = '')
    {
        $chainName = $connector->getPlatform();
        /** @var CommandQueryData $tx */
        $tx = Transaction::init($connector);
        $tx->setParamByKey(
            '0:operations:0',
            [
                ChainOperations::OPERATION_CONTENT,
                [
                    'parent_author'   => $parentAuthor,
                    'parent_permlink' => $parentPermlink,
                    'author'          => $author,
                    'permlink'        => $permlink,
                    'title'           => $title,
                    'body'            => $body,
                    'curation_percent'=> $curationPercent,
                    'json_metadata'   => $jsonMetadata,
                    'extensions'      => empty($beneficiaries) ? [] : [[0, ["beneficiaries" => $beneficiaries]]]
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