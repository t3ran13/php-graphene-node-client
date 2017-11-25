<?php

namespace GrapheneNodeClient\Tools;


use GrapheneNodeClient\Commands\CommandQueryDataInterface;
use GrapheneNodeClient\Tools\ChainOperations\ChainOperations;
use GrapheneNodeClient\Tools\ChainOperations\OperationSerializer;
use StephenHill\Base58;
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


    public static function signTransaction($chainName, CommandQueryDataInterface $trx, $privKyes)
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

        //serialize only transaction data
        $serBuffer = OperationSerializer::serializeTransaction($trxParams, new Buffer());

        //serialize only operations data
        foreach ($trxParams[0]['operations'] as $operation) {
            $opData = $operation[1];
            OperationSerializer::serializeOperation($operation[0], $opData, $serBuffer);
        }
//        echo "\n" . print_r($serBuffer->getBuffer('H', 0, $serBuffer->getCurrentOffset()), true) . '<pre>'; die; //FIXME delete it


//        $cid = hex2bin(self::getChainId($chainName));
        $cidBuffer = new Buffer();
        $cidBuffer->writeVHexStringBE(self::getChainId($chainName));
        $sigBuffer = $cidBuffer->concat($cidBuffer, $serBuffer);
//        echo '<pre>' . print_r($sigBuffer, true) . '<pre>'; die; //FIXME delete it
//        echo "\n" . print_r($sigBuffer->getBuffer('H', 0, $sigBuffer->getCurrentOffset()), true) . '<pre>'; die; //FIXME delete it

        foreach ($privKyes as $keyName => $key) {
            $privateKey = self::PrivateKeyFromWif($key);

            $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

            $msg32 = hash('sha256', $sigBuffer->getBuffer('H', 0, $sigBuffer->getCurrentOffset()), true);
            $privateKey = pack("H*", $privateKey);
            /** @var resource $signature */
            $signature = '';

            if (secp256k1_ecdsa_sign($context, $signature, $msg32, $privateKey) != 1) {
                throw new \Exception("Failed to create signature");
            }

            $serialized = '';
            secp256k1_ecdsa_signature_serialize_der($context, $serialized, $signature);
            echo sprintf("Produced signature: %s \n", bin2hex($serialized));
            echo '<pre>' . print_r(123, true) . '<pre>'; //FIXME delete it
        }

        die;




        return $out;
    }

    public static function PrivateKeyFromWif($privWif) {
//        var private_wif = new Buffer(base58.decode(_private_wif));
//        var version = private_wif.readUInt8(0);
//        assert.equal(0x80, version, `Expected version ${0x80}, instead got ${version}`);
//        // checksum includes the version
//        var private_key = private_wif.slice(0, -4);
//        var checksum = private_wif.slice(-4);
//        var new_checksum = hash.sha256(private_key);
//        new_checksum = hash.sha256(new_checksum);
//        new_checksum = new_checksum.slice(0, 4);
//        if (checksum.toString() !== new_checksum.toString())
//            throw new Error('Invalid WIF key (checksum miss-match)')
//
//        private_key = private_key.slice(1);
//return PrivateKey.fromBuffer(private_key);
//echo '<pre>' . print_r($privWif, true) . '<pre>'; die; //FIXME delete it

        //checking wif version
        $base58 = new Base58();
        $wifBuffer = new Buffer();
        $wifBuffer->write($base58->decode($privWif));
        $version = $wifBuffer->readInt8(0);
        if ($wifBuffer->readInt8(0) !== 128) {
            //        assert.equal(0x80, version, `Expected version ${0x80}, instead got ${version}`);
            throw new \Exception('Expected version 128, instead got ' . $version);
        }

        //checking WIF checksum
        $private_key = $wifBuffer->read(0, $wifBuffer->length() - 4);
        $checksum = $wifBuffer->read($wifBuffer->length() - 4, 4);
        $new_checksum = hash('sha256', $private_key, true);
        $new_checksum = hash('sha256', $new_checksum, true);
        $new_checksum = substr($new_checksum, 0, 4);
        if ($new_checksum !== $checksum) {
            throw new \Exception('Invalid WIF key (checksum miss-match)');
        }

        //getting private_key
        $private_key = substr($private_key, 1);
        echo '<pre>' . print_r(strlen($private_key), true) . '<pre>'; die; //FIXME delete it
        echo "\n" . print_r($wifBuffer->getBuffer('H', 0, $wifBuffer->getCurrentOffset()), true) . '<pre>'; die; //FIXME delete it

        return $answer;
    }
}