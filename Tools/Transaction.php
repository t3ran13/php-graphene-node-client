<?php


namespace GrapheneNodeClient\Tools;


use GrapheneNodeClient\Commands\CommandQueryData;
use GrapheneNodeClient\Commands\CommandQueryDataInterface;
use GrapheneNodeClient\Commands\DataBase\GetBlockCommand;
use GrapheneNodeClient\Commands\DataBase\GetDynamicGlobalPropertiesCommand;
use GrapheneNodeClient\Commands\Login\LoginCommand;
use GrapheneNodeClient\Connectors\ConnectorInterface;
use GrapheneNodeClient\Connectors\WebSocket\GolosWSConnector;
use GrapheneNodeClient\Connectors\WebSocket\SteemitWSConnector;
use GrapheneNodeClient\Tools\ChainOperations\OperationSerializer;
use TrafficCophp\ByteBuffer\Buffer;

class Transaction
{
    const CHAIN_STEEM = ConnectorInterface::PLATFORM_STEEMIT;
    const CHAIN_GOLOS = ConnectorInterface::PLATFORM_GOLOS;
    const CHAIN_ID    = [
        self::CHAIN_GOLOS => '782a3039b478c839e4cb0c941ff4eaeb7df40bdd68bd441afd444b9da763de12',
        self::CHAIN_STEEM => '0000000000000000000000000000000000000000000000000000000000000000'
    ];

    public static function getChainId($chainName)
    {
        $answer = null;
        if (in_array($chainName, [self::CHAIN_GOLOS, self::CHAIN_STEEM], true)) {
            $answer = self::CHAIN_ID[$chainName];
        }

        return $answer;
    }

    /**
     * @param string $chainName
     *
     * @return CommandQueryData
     * @throws \Exception
     */
    public static function init($chainName)
    {
        $connector = null;
        $tx = null;
        if (self::CHAIN_GOLOS === $chainName) {
            $connector = new GolosWSConnector();
        } elseif (self::CHAIN_STEEM === $chainName) {
            $connector = new SteemitWSConnector();
        }

        $command = new LoginCommand($connector);
        $commandQueryData = new CommandQueryData();
        $commandQueryData->setParams(
            ['', '']
        );
        $isLogin = $command->execute(
            $commandQueryData,
            'result'
        );

        if ($isLogin === true) {
            $command = new GetDynamicGlobalPropertiesCommand($connector);
            $commandQueryData = new CommandQueryData();
            $properties = $command->execute(
                $commandQueryData,
                'result'
            );

//            $properties = [
//                'head_block_number' => '11836693',
//                'time'              => '2017-12-04T20:31:24',
//            ];

            if (self::CHAIN_GOLOS === $chainName) {
                $blockId = $properties['head_block_number'] - 2;
            } elseif (self::CHAIN_STEEM === $chainName) {
                $blockId = $properties['last_irreversible_block_num'];
            }
            $command = new GetBlockCommand($connector);
            $commandQueryData = new CommandQueryData();
            $commandQueryData->setParamByKey('0', $blockId);
            $block = $command->execute(
                $commandQueryData,
                'result'
            );

            if (isset($properties['head_block_number']) && isset($block['previous'])) {
                if (self::CHAIN_GOLOS === $chainName) {
                    $refBlockNum = ($properties['head_block_number'] - 3) & 0xFFFF;
                } elseif (self::CHAIN_STEEM === $chainName) {
                    $refBlockNum = ($properties['last_irreversible_block_num'] - 1) & 0xFFFF;
                }

                $tx = new CommandQueryData();
                $buf = new Buffer();
                $buf->write(hex2bin($block['previous']));

                $tx->setParams(
                    [[
                        'ref_block_num'    => $refBlockNum,
                        'ref_block_prefix' => $buf->readInt32lE(4),
                        'expiration'       => (new \DateTime($properties['time']))->add(new \DateInterval('PT1M'))->format('Y-m-d\TH:i:s\.000'),
                        'operations'       => [],
                        'extensions'       => [],
                        'signatures'       => []
                    ]]
                );
            }
        }

        if (!($tx instanceof CommandQueryDataInterface)) {
            throw new \Exception('cant init Tx');
        }
//        $properties2 = [
//            'ref_block_num'    => '40210',
//            'ref_block_prefix' => '1950645087',
//            'expiration'       => '2017-12-04T20:32:24.000',
//        ];
//        echo '<pre>' . var_dump($tx->getParams(), $properties2) . '<pre>'; //FIXME delete it

        return $tx;
    }

    /**
     * @param string                    $chainName
     * @param CommandQueryDataInterface $trxData
     *
     * @return string
     */
    public static function getTxMsg($chainName, CommandQueryDataInterface $trxData)
    {

        //serialize transaction
        $trxParams = $trxData->getParams();
        $serBuffer = OperationSerializer::serializeTransaction($trxParams, new Buffer());
        $serializedTx = self::getChainId($chainName) . bin2hex($serBuffer->read(0, $serBuffer->length()));

//        echo "\n" . var_dump(
//                '$serializedTx'
//            ); //FIXME delete it
//        echo "\n" . var_dump(
//                $serializedTx,
//                '782a3039b478c839e4cb0c941ff4eaeb7df40bdd68bd441afd444b9da763de12129d5f7b4474d8b0255a01000867756573743132330966697265706f77657254676f6c6f7369742d76656e692d766964692d766963692d676f6c6f73666573742d323031362d746f6765746865722d77652d6d6164652d69742d68617070656e2d7468616e6b2d796f752d676f6c6f7369616e73102700',
//                $serializedTx === '782a3039b478c839e4cb0c941ff4eaeb7df40bdd68bd441afd444b9da763de12129d5f7b4474d8b0255a01000867756573743132330966697265706f77657254676f6c6f7369742d76656e692d766964692d766963692d676f6c6f73666573742d323031362d746f6765746865722d77652d6d6164652d69742d68617070656e2d7468616e6b2d796f752d676f6c6f7369616e73102700'
//            ); //FIXME delete it

        return $serializedTx;
    }


    /**
     * @param string                    $chainName
     * @param CommandQueryDataInterface $trxData
     * @param string[]                  $privateWIFs
     *
     * @return mixed
     * @throws \Exception
     */
    public static function sign($chainName, CommandQueryDataInterface $trxData, $privateWIFs)
    {
        $msg = self::getTxMsg($chainName, $trxData);

        foreach ($privateWIFs as $keyName => $privateWif) {
            $index = count($trxData->getParams()[0]['signatures']);
            /** @var CommandQueryData $trxData */
            $trxData->setParamByKey('0:signatures:' . $index, self::signOperation($msg, $privateWif));
        }

        return $trxData;
    }


    /**
     * @param string $msg serialized Tx with prefix chain id
     * @param string $privateWif
     *
     * @return string hex
     * @throws \Exception
     */
    protected static function signOperation($msg, $privateWif)
    {
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        $msg32 = hash('sha256', $msg, true);
//            $privateKey = hash('sha256', bin2hex(self::PrivateKeyFromWif($privateWif)), true);
        $privateKey = Auth::PrivateKeyFromWif($privateWif);

//        echo "\n" . var_dump(
//                '$privateKey'
//            ); //FIXME delete it
//        echo "\n" . var_dump(
//                bin2hex($privateKey),
//                '5027264a7f77bfdde0b6385af59d5e46bac8451c367d40b12bfaa5e69a687d26',
//                bin2hex($privateKey) === '5027264a7f77bfdde0b6385af59d5e46bac8451c367d40b12bfaa5e69a687d26'
//            ); //FIXME delete it

        /** @var resource $signature */
        $signatureRec = '';
        $i = 0;
        while (true) {
            if ($i === 1) {
                //sing always the same
                throw new \Exception("Can't to find canonical signature, {$i} ties");
            }
            echo "\n i=" . print_r($i++, true) . '<pre>'; //FIXME delete it

            if (secp256k1_ecdsa_sign_recoverable($context, $signatureRec, $msg32, $privateKey) !== 1) {
                throw new \Exception("Failed to create recoverable signature");
            }

            $signature = '';
            if (secp256k1_ecdsa_recoverable_signature_convert($context, $signature, $signatureRec) !== 1) {
                throw new \Exception("Failed to create signature");
            }
            $der = '';
            if (secp256k1_ecdsa_signature_serialize_der($context, $der, $signature) !== 1) {
                throw new \Exception("Failed to create DER");
            }
            if (self::isSignatureCanonical($der)) {
                break;
            }
        }

        $serializedSig = '';
        $recid = 0;
        secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $signatureRec, $serializedSig, $recid);

        $serializedSig = hex2bin(base_convert($recid + 4 + 27, 10, 16)) . $serializedSig;

        $length = strlen($serializedSig);
        if ($length !== 65) {
            throw new \Exception('Expecting 65 bytes for Tx signature, instead got ' . $length);
        }

        return bin2hex($serializedSig);
    }


    /**
     * @param string $der string of binary
     *
     * @return bool
     */
    public static function isSignatureCanonical($der)
    {
        $buffer = new Buffer();
        $buffer->write($der);
//        lenR = der[3];
//        lenS = der[5 + lenR];
//        if (lenR === 32 && lenS === 32) {
        $lenR = $buffer->readInt8(3);
        $lenS = $buffer->readInt8(5 + $lenR);
//        echo "\n" . var_dump($lenR, $lenS) . '<pre>'; //FIXME delete it

        return $lenR === 32 && $lenS === 32;
    }




//    /**
//     * @param string $serializedSig binary string serialized signature
//     * @param string $skip skip the first byte with sing technical data (4 - compressed | 27 - compact)
//     *
//     * @return bool
//     */
//    public static function isSignatureCanonical($serializedSig, $skip)
//    {
//        //             test after secp256k1_ecdsa_recoverable_signature_serialize_compact
//        //        public static bool IsCanonical(byte[] sig, int skip)
//        //        {
//        //        return !((sig[skip + 0] & 0x80) > 0)
//        //        && !(sig[skip + 0] == 0 && !((sig[skip + 1] & 0x80) > 0))
//        //        && !((sig[skip + 32] & 0x80) > 0)
//        //        && !(sig[skip + 32] == 0 && !((sig[skip + 33] & 0x80) > 0));
//        //        }
//
//        $buffer = new Buffer();
//        $buffer->write($serializedSig);
//
//        return !(($buffer->readInt8($skip + 0, 1) & 0x80) > 0)
//            && !($buffer->readInt8($skip + 0, 1) === 0 && !(($buffer->readInt8($skip + 1, 1) & 0x80) > 0))
//            && !(($buffer->readInt8($skip + 32, 1) & 0x80) > 0)
//            && !($buffer->readInt8($skip + 32, 1) === 0 && !(($buffer->readInt8($skip + 33, 1) & 0x80) > 0));
//    }
}