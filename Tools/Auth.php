<?php

namespace GrapheneNodeClient\Tools;


use GrapheneNodeClient\Commands\CommandQueryDataInterface;
use GrapheneNodeClient\Tools\ChainOperations\ChainOperations;
use GrapheneNodeClient\Tools\ChainOperations\OperationSerializer;
use TrafficCophp\ByteBuffer\Buffer;

class Auth
{
    const CHAIN_STEEM = 'steem';
    const CHAIN_GOLOS = 'golos';
    const CHAIN_ID = [
        self::CHAIN_GOLOS => '782a3039b478c839e4cb0c941ff4eaeb7df40bdd68bd441afd444b9da763de12',
        self::CHAIN_STEEM => '0000000000000000000000000000000000000000000000000000000000000000'
    ];

    public static function getChainId($chainName) {
        $answer = null;
        if (in_array($chainName, [self::CHAIN_GOLOS, self::CHAIN_STEEM], true)) {
            $answer = self::CHAIN_ID[$chainName];
        }

        return $answer;
    }


    public static function signTransaction($chainName, CommandQueryDataInterface $trx)
    {
//        var signatures = [];
//        if (trx.signatures) {
//            signatures = [].concat(trx.signatures);
//        }
//
//        var cid = new Buffer(config.get('chain_id'), 'hex');
//        var buf = transaction.toBuffer(trx);
//
//        for (var key in keys) {
//        var sig = Signature.signBuffer(Buffer.concat([cid, buf]), keys[key]);
//        signatures.push(sig.toBuffer())
//	}
//
//return signed_transaction.toObject(Object.assign(trx, { signatures: signatures }))


        $signatures = [];
        $trxParams = $trx->getParams();
        if (isset($trxParams[0]['signatures'])) {
            $signatures = $trxParams[0]['signatures'];
        }

        echo '<pre>' . print_r($trxParams, true) . '<pre>';  //FIXME delete it

//        $cid = hex2bin(self::getChainId($chainName));
        $cid = self::getChainId($chainName);

        //only transaction data
        $buf = OperationSerializer::serializeTransaction($trxParams, new Buffer());

        //only operations data
        foreach ($trxParams[0]['operations'] as $operation) {
            $opData = $operation[1];
            OperationSerializer::serializeOperation($operation[0], $opData, $buf);
        }
        echo "\n" . print_r($buf->getBuffer('H', 0, $buf->getCurrentOffset()), true) . '<pre>'; die; //FIXME delete it




        return $out;
    }
}