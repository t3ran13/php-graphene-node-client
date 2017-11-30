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


        //serialize only transaction data
        $trxParams = $trx->getParams();
        echo '<pre>' . print_r($trxParams, true) . '<pre>';  //FIXME delete it
        $serBuffer = OperationSerializer::serializeTransaction($trxParams, new Buffer());


//        echo '<pre>' . var_dump(
//                $serBuffer->length(),
//            bin2hex($serBuffer->read(0, $serBuffer->length()))
//            ) . '<pre>'; die; //FIXME delete it
        $serializedTx = self::getChainId($chainName) . bin2hex($serBuffer->read(0, $serBuffer->length()));

        foreach ($privKyes as $keyName => $privateWif) {
            $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

//            $msg32 = hash('sha256', $sigBuffer->read(0, $sigBuffer->getCurrentOffset()), true); //может так??
            $msg32 = hash('sha256', $serializedTx, true);
            //wiff 5KSxPLJs1FkoWbUptnwZ6816xBQe2byYQR5jEV2tSGWDWpH2r6F
            //private key d6f527c2790a52c8b388fcb277382013916df0ab2f9819eb7678984dfe82f5b5
            $privateKey = self::PrivateKeyFromWif($privateWif);
            echo '<pre>' . var_dump(bin2hex($privateKey), 'd6f527c2790a52c8b388fcb277382013916df0ab2f9819eb7678984dfe82f5b5') . '<pre>'; die; //FIXME delete it
//            echo '<pre>' . var_dump(strlen($msg32), strlen($privateKey)) . '<pre>'; die; //FIXME delete it

            /** @var resource $signature */
            $signature = '';
            $i = 0;
            while (true) {
                if ($i === 100) {
                    throw new \Exception("Can't to find canonical signature, {$i} ties");
                }
                echo "\n i=" . print_r($i++, true) . '<pre>'; //FIXME delete it

                if (secp256k1_ecdsa_sign_recoverable($context, $signature, $msg32, $privateKey) !== 1) {
                    throw new \Exception("Failed to create signature");
                }

                $serializedSig = '';
                $recid = 0;
                secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $serializedSig, $recid);

                if (self::isSignatureCanonical($serializedSig, 0)) {
                    break;
                }
            }

//            int recoveryId = -1;
//            var sigptr = new byte[64];
//            var msg32 = GetMessageHash(data);
//            var t = sign_compact(Ctx, msg32, seckey, sigptr, ref recoveryId);
//            //4 - compressed | 27 - compact
//            var sRez = Hex.Join(new[] { (byte)(recoveryId + 4 + 27) }, sigptr);
//return sRez;

            //for $recid = -1;
            $serializedSig = hex2bin(base_convert($recid + 4 + 27, 10, 16)) . $serializedSig;
            //OR for $recid = 0;
//            $serializedSig = hex2bin(base_convert($recid + 4 + 27, 10, 16)) . substr($serializedSig, 1);
//            echo '<pre>' . print_r(strlen($serializedSig), true) . '<pre>'; die; //FIXME delete it

            $length = strlen($serializedSig);
            if ($length !== 65) {
                throw new \Exception('Expecting 65 bytes for Tx signature, instead got ' . $length);
            }

            $trxParams[0]['signatures'][] = bin2hex($serializedSig);
        }

        return $trxParams;
    }


    /**
     * @param string $privateWif Private (posting key?) wif
     *
     * @return string outputs Private key raw binary data
     * @throws \Exception
     */
    public static function PrivateKeyFromWif($privateWif) {
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

        //checking wif version
        $base58 = new Base58();
        $wifBuffer = new Buffer();
        $wifBuffer->write($base58->decode($privateWif));
        $version = $wifBuffer->readInt8(0);
        if ($version !== 128) {
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
//        $private_key = substr(strrev($wifBuffer->read(0, $wifBuffer->length())), 1, -4);
        $private_key = substr($private_key, 1);
//        if (32 !== buf.length) {
//            console.log(`WARN: Expecting 32 bytes, instead got ${buf.length}, stack trace:`, new Error().stack);
//        }
        $length = strlen($private_key);
        if ($length !== 32) {
            throw new \Exception('Expecting 32 bytes for private_key, instead got ' . $length);
        }

        return $private_key;
    }


//    public static function isSignatureCanonical($serializedSig)
//    {
//        $rl = 32;
//        $r = substr($serializedSig, 0, 32);
//        if (ord($serializedSig[0]) > 0x80) {
//            $rl++;
//            $r = "\x00" . $r;
//        }
//        $sl = 32;
//        $s = substr($serializedSig, 32, 32);
//        if (ord($serializedSig[32]) > 0x80) {
//            $sl++;
//            $s = "\x00" . $s;
//        }
//        $t = 4 + $rl + $sl;
//        $der = "\x30" . chr($t) . "\x02" . chr($rl) . $r . "\x02" . chr($sl) . $s;
//
////        lenR = der[3];
////        lenS = der[5 + lenR];
////        if (lenR === 32 && lenS === 32) {
//        $lenR = (int)base_convert(unpack('H*', $der[3], 0)[1], 16, 10);
//        $lenS = (int)base_convert(unpack('H*', $der[5 + $lenR], 0)[1], 16, 10);
//        echo "\n" . var_dump($lenR, $lenS) . '<pre>'; //FIXME delete it
//
//        return $lenR === 32 && $lenS === 32;
//    }


    /**
     * @param string $serializedSig binary string serialized signature
     * @param string $skip skip the first byte with sing technical data (4 - compressed | 27 - compact)
     *
     * @return bool
     */
    public static function isSignatureCanonical($serializedSig, $skip)
    {
        //             test after secp256k1_ecdsa_recoverable_signature_serialize_compact
        //        public static bool IsCanonical(byte[] sig, int skip)
        //        {
        //        return !((sig[skip + 0] & 0x80) > 0)
        //        && !(sig[skip + 0] == 0 && !((sig[skip + 1] & 0x80) > 0))
        //        && !((sig[skip + 32] & 0x80) > 0)
        //        && !(sig[skip + 32] == 0 && !((sig[skip + 33] & 0x80) > 0));
        //        }

        $buffer = new Buffer();
        $buffer->write($serializedSig);

        return !(($buffer->readInt8($skip + 0, 1) & 0x80) > 0)
            && !($buffer->readInt8($skip + 0, 1) === 0 && !(($buffer->readInt8($skip + 1, 1) & 0x80) > 0))
            && !(($buffer->readInt8($skip + 32, 1) & 0x80) > 0)
            && !($buffer->readInt8($skip + 32, 1) === 0 && !(($buffer->readInt8($skip + 33, 1) & 0x80) > 0));
    }
}