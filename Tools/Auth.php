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
            $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

//            $msg32 = hash('sha256', $sigBuffer->read(0, $sigBuffer->getCurrentOffset()), true); //может так??
            $msg32 = hash('sha256', $sigBuffer->getBuffer('H', 0, $sigBuffer->getCurrentOffset()), true);
            $privateKey = self::PrivateKeyFromWif($key);

            /** @var resource $signature */
            $signature = '';
            $i = 0;
            while (true) {
                echo "\n i=" . print_r($i, true) . '<pre>'; //FIXME delete it
                if ($i++ > 3000) {
                    break;
//                    throw new \Exception("Can't got canonical signature");
                }

//                if (secp256k1_ecdsa_sign($context, $signature, $msg32, $privateKey) !== 1) {
                if (secp256k1_ecdsa_sign_recoverable($context, $signature, $msg32, $privateKey) !== 1) {
                    throw new \Exception("Failed to create signature");
                }

//                $serializedSig = '';
//                secp256k1_ecdsa_signature_serialize_der($context, $serializedSig, $signature);
                //answer looks canonical by default

                $serializedSig = '';
                $recid = -1;
                secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signature, $serializedSig, $recid);
//                echo '<pre>' . print_r(bin2hex($serializedSig), true) . '<pre>'; die; //FIXME delete it

//                if (self::isSignatureCanonical($serializedSig)) {
                if (self::isSignatureCanonical($serializedSig)) {
//                    break;
                }
            }

//            int recoveryId = -1;
//            var sigptr = new byte[64];
//            var msg32 = GetMessageHash(data);
//            var t = sign_compact(Ctx, msg32, seckey, sigptr, ref recoveryId);
//            //4 - compressed | 27 - compact
//            var sRez = Hex.Join(new[] { (byte)(recoveryId + 4 + 27) }, sigptr);
//return sRez;
            $buf = new Buffer();
//            $buf->writeInt8(4);
//            $buf->writeInt8(27);
            $buf->writeInt8($recid + 4 + 27);
            $serializedSig = $buf->read(0, 1) . $serializedSig;
//            $serializedSig .= $buf->read(0, 2);
//            echo sprintf("Produced signature: %s \n", bin2hex($serializedSig));
//            echo '<pre>' . print_r($serializedSig, true) . '<pre>'; //FIXME delete it


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
////        echo "\n" . var_dump($lenR, $lenS) . '<pre>'; //FIXME delete it
//
//        return $lenR === 32 && $lenS === 32;
//    }


    public static function isSignatureCanonical($serializedSig)
    {
//        return !(sig[0] & 0x80)
//            && !(sig[0] == 0 && !(sig[1] & 0x80))
//            && !(sig[32] & 0x80)
//            && !(sig[32] == 0 && !(sig[33] & 0x80));

//        echo '<pre>' . var_dump(unpack('H*', $serializedSig[0], 0)[1] & 0x80, true) . '<pre>'; die; //FIXME delete it
//        return !(unpack('H*', $serializedSig[0], 0)[1] & 0x80)
//            && !(unpack('H*', $serializedSig[0], 0)[1] === 0 && !(unpack('H*', $serializedSig[1], 0)[1] & 0x80))
//            && !($unpack('H*', $serializedSig[32], 0)[1] & 0x80)
//            && !($unpack('H*', $serializedSig[32], 0)[1] === 0 && !($unpack('H*', $serializedSig[1], 0)[1] & 0x80));
    }
}